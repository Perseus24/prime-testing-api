<?php

namespace App\Services;

use App\Models\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class TestCaseImporter
{
    private const COLUMNS = ['code', 'title', 'module', 'platform', 'priority', 'preconditions', 'steps', 'expected'];

    private const RULES = [
        'code' => ['required', 'string', 'max:50'],
        'title' => ['required', 'string', 'max:255'],
        'module' => ['nullable', 'string', 'max:100'],
        'platform' => ['required', 'in:web,mobile'],
        'priority' => ['required', 'in:low,medium,high'],
        'preconditions' => ['array'],
        'steps' => ['required', 'array', 'min:1'],
        'expected' => ['required', 'array', 'min:1'],
    ];

    /**
     * Read the CSV and return its rows, each marked new, duplicate or error.
     * Saves nothing. Used by the preview and import endpoints.
     */
    public function preview(UploadedFile $file, bool $hasHeader = true): array
    {
        $rows = array_map(fn ($r) => $this->normalize($r), $this->parse($file, $hasHeader));

        $existing = TestCase::whereIn('code', array_column($rows, 'code'))->pluck('code')->all();
        $seen = [];

        return array_map(function ($data, $i) use ($existing, &$seen, $hasHeader) {
            $errors = $this->validateRow($data);

            if (in_array($data['code'], $seen, true)) {
                $errors[] = 'This test case ID appears more than once in the file.';
            }
            $seen[] = $data['code'];

            $status = $errors ? 'error' : (in_array($data['code'], $existing, true) ? 'duplicate' : 'new');

            return [
                'line' => $i + ($hasHeader ? 2 : 1), // the row number as the person sees it in their spreadsheet
                'status' => $status,
                'errors' => $errors,
                'data' => $data,
            ];
        }, $rows, array_keys($rows));
    }

    /**
     * Run preview, then save the rows in a transaction.
     * $onDuplicate is 'skip' or 'update'.
     * Returns the counts: created, updated, skipped, failed.
     */
    public function import(UploadedFile $file, string $onDuplicate = 'skip', bool $hasHeader = true): array
    {
        $rows = $this->preview($file, $hasHeader);
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0];

        DB::transaction(function () use ($rows, $onDuplicate, &$counts) {
            $sort = (int) TestCase::max('sort');

            foreach ($rows as $row) {
                $data = $row['data'];

                if ($row['status'] === 'error') {
                    $counts['failed']++;
                } elseif ($row['status'] === 'duplicate') {
                    if ($onDuplicate === 'update') {
                        // load the model, not a query update, so the array casts still apply
                        TestCase::where('code', $data['code'])->firstOrFail()->update($data);
                        $counts['updated']++;
                    } else {
                        $counts['skipped']++;
                    }
                } else {
                    TestCase::create([...$data, 'sort' => ++$sort]);
                    $counts['created']++;
                }
            }
        });

        return $counts;
    }

    /** Read the file into raw rows (arrays keyed by column name). */
    private function parse(UploadedFile $file, bool $hasHeader): array
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');

        if ($hasHeader) {
            $csv->setHeaderOffset(0); // the first row becomes the column names
            return iterator_to_array($csv->getRecords(), false);
        }

        // no header row, so the columns are read by position
        return iterator_to_array($csv->getRecords(self::COLUMNS), false);
    }

    /** Turn one raw row into the shape the database expects (splits the , cells into arrays). */
    private function normalize(array $row): array
    {
        $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

        // "a, b, c" becomes ['a', 'b', 'c'], with blank items dropped
        $list = fn (?string $cell) => array_values(array_filter(
            array_map('trim', explode(',', $cell ?? '')),
            fn ($item) => $item !== ''
        ));

        return [
            'code' => $row['code'] ?? '',
            'title' => $row['title'] ?? '',
            'module' => ($row['module'] ?? '') ?: null,
            'platform' => strtolower($row['platform'] ?? ''),
            'priority' => strtolower($row['priority'] ?? ''),
            'preconditions' => $list($row['preconditions'] ?? ''),
            'steps' => $list($row['steps'] ?? ''),
            'expected' => $list($row['expected'] ?? ''),
        ];
    }

    /** Validate one normalized row with the same rules as StoreTestCaseRequest. */
    private function validateRow(array $row): array
    {
        return Validator::make($row, self::RULES)->errors()->all();
    }
}