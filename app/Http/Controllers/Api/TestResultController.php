<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResultRequest;
use App\Models\TestCase;
use App\Models\Tester;
use Illuminate\Http\Request;
use League\Csv\Writer;

class TestResultController extends Controller
{
    public function adminIndex(Request $request) {
        $testers = Tester::where('role', 'tester')->get();
        $tests = TestCase::orderBy('sort')
            ->where('hide', false)
            ->with('results')
            ->get()
            ->map(fn ($testCase) => [
                'code' => $testCase->code,
                'title' => $testCase->title,
                'module' => $testCase->module,
                'priority' => $testCase->priority,
                'platform' => $testCase->platform,
                'results' => $testers->map(fn ($tester) => 
                    $testCase->results()->firstWhere('tester_id', $tester->id)?->result
                )
            ]);

        return [
            'testers' => $testers->map(fn ($t) => ['code' => $t->code, 'name' => $t->name]),
            'data' => $tests,
        ];
    }

    public function store(StoreResultRequest $request, TestCase $testCase) {
        $data = $request->validated();

        if ($data['result'] !== 'failed') {
            $data = [...$data, 'severity' => null, 'frequency' => null, 'device' => null];
        }

        $result = $request->user()->results()->updateOrCreate(
            ['test_case_id' => $testCase->id],
            $data
        );

        return response()->json($result);
    }   

    public function submit(Request $request) {
        $request->user()->update(['submitted_at' => now()]);
        return ['success' => true];
    }

    public function export(){
        $testers = Tester::where('role', 'tester')->get();
        $tests = TestCase::orderBy('sort')
            ->where('hide', false)
            ->with('results')
            ->get();

        return response()->streamDownload(function () use ($testers, $tests) {
            $csv = Writer::createFromStream(fopen('php://output', 'w'));
            $csv->setOutputBOM(Writer::BOM_UTF8);

            $testersById = $testers->keyBy('id');

            $csv->insertOne([
                'Code', 'Title', 'Module', 'Priority', 'Platform',
                'Tester', 'Tester name', 'LGU',
                'Result', 'Notes', 'Severity', 'Frequency', 'Device', 'Submitted at',
            ]);

            foreach ($tests as $test) {
                foreach ($test->results as $result) {
                    $tester = $testersById->get($result->tester_id);
                    if (! $tester) continue; // skips results from admin accounts

                    $csv->insertOne([
                        $test->code, $test->title, $test->module, $test->priority, $test->platform,
                        $tester->code, $tester->name, $tester->lgu,
                        $result->result, $result->notes, $result->severity, $result->frequency, $result->device,
                        $result->updated_at?->format('Y-m-d H:i'),
                    ]);
                }
            }
    }, 'results-matrix.csv', ['Content-Type' => 'text/csv']);

    }
}
