<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestCase;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function store(Request $request, TestCase $testCase) {
        $data = $request->safe()->except('screenshot');

        if ($data['result'] === 'fail') {
            $data = [
                ...$data,
                'severity' => null,
                'frequency' => null,
                'device' => null,
            ];
        }

        if ($request->hasFile('screenshot')) {
            $data['screenshot_path'] = $request->file('screenshot')->store('screenshots', 'public');
        }

        return $request->user()->resuts()->updateOrCreate([
            ['test_case_id' => $testCase->id],
            $data
        ]);
    }   

    public function submit(Request $request) {
        $request->user()->update(['submitted_at' => now()]);
        return ['success' => true];
    }
}
