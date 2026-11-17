<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResultRequest;
use App\Models\TestCase;
use App\Models\Tester;
use Illuminate\Http\Request;

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
}
