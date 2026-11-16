<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestCaseRequest;
use App\Models\TestCase;
use App\Models\Tester;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    public function index(Request $request) {

        $testResults = $request->user()
            ->results()
            ->get()
            ->keyBy('test_case_id');

        return TestCase::orderBy('sort')->get()->map(fn ($testCase) => [
            'code' => $testCase->code,
            'title' => $testCase->title,
            'module' => $testCase->module,
            'priority' => $testCase->priority,
            'platform' => $testCase->platform,
            'number_of_steps' => $testCase->numberOfSteps(),
            'result' => $testResults[$testCase->id]->result ?? null
        ]);
    }

    public function adminIndex() {
        $numberOfTesters = Tester::where('role', 'tester')->count();
        $tests = TestCase::orderBy('sort')
            ->withCount('results')
            ->get()->map(fn ($testCase) => [
            'code' => $testCase->code,
            'title' => $testCase->title,
            'module' => $testCase->module,
            'platform' => $testCase->platform,
            'priority' => $testCase->priority,
            'result_count' => $testCase->results_count,
            'number_of_steps' => $testCase->numberOfSteps()
        ]);

        return [
            'data' => $tests,
            'meta' => [
                'number_of_testers' => $numberOfTesters
            ]
        ];
    }

    public function show(Request $request, TestCase $testCase) {
        return [
            ...$testCase->toArray(),
            'my_result' => $request->user()->results()
                ->where('test_case_id', $testCase->id)
                ->first()
        ];
    }


    public function store(StoreTestCaseRequest $request) {
        $testCase = TestCase::create([
            ...$request->validated(),
            'sort' => (TestCase::max('sort') ?? 0) + 1,
        ]);

        return response()->json($testCase, 201);
    }
}
