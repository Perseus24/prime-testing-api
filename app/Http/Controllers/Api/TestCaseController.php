<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestCase;
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
            'platform' => $testCase->platform,
            'result' => $testResults[$testCase->id]->result ?? null
        ]);
    }

    public function show(Request $request, TestCase $testCase) {
        return [
            ...$testCase->toArray(),
            'my_result' => $request->user()->results()
                ->where('test_case_id', $testCase->id)
                ->first()
        ];
    }
}
