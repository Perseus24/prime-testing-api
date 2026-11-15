<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTesterRequest;
use App\Http\Requests\UpdateTesterRequest;
use App\Models\TestCase;
use App\Models\Tester;
use Illuminate\Http\Request;

class TesterController extends Controller
{
    public function index(Request $request) {
        $testers = Tester::all()
            ->map(fn ($tester) => [
                'id' => $tester->id,
                'code' => $tester->code,
                'name' => $tester->name,
                'lgu' => $tester->lgu,
                'role' => $tester->role,
                'position' => $tester->position,
                'total_submitted_results' => $tester->results()->count(),
            ]);

        $totalTestCases = TestCase::all()->count();
        return [
            'data' => $testers,
            'meta' => [
                'total_test_cases' => $totalTestCases
            ]
        ];
    }

    public function store(StoreTesterRequest $request) {
        $plain = Tester::generateAccessCode();

        $tester = Tester::create([
            ...$request->validated(),
            'access_code' => $plain,
        ]);

        return response()->json([
            'tester' => $tester,
            'access_code' => $plain, 
        ], 201);
    }

    public function update(UpdateTesterRequest $request, Tester $tester)
    {
        $tester->update($request->validated());

        if ($tester->wasChanged('role')) {
            $tester->tokens()->delete();
        }

        return response()->json($tester);
    }

    public function resetCode(Tester $tester)
    {
        $plain = Tester::generateAccessCode();

        $tester->update(['access_code' => $plain]);
        $tester->tokens()->delete(); 

        return response()->json(['access_code' => $plain]);
    }
}
