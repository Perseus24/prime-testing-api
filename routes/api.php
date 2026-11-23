<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestCaseController;
use App\Http\Controllers\Api\TestCaseImportController;
use App\Http\Controllers\Api\TesterController;
use App\Http\Controllers\Api\TestResultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('test-cases', [TestCaseController::class, 'index']);
        Route::get('test-cases/{testCase:code}', [TestCaseController::class, 'show']);
        Route::post('test-cases/{testCase:code}/result', [TestResultController::class, 'store']);
        Route::post('submit', [TestResultController::class, 'submit']);
    });

    Route::middleware(['auth:sanctum', 'abilities:admin'])->prefix('admin')->group(function () {
        Route::get('results/export', [TestResultController::class, 'export']);
        Route::get('results', [TestResultController::class, 'adminIndex']);

        Route::get('test-cases', [TestCaseController::class, 'adminIndex']);
        Route::post('test-cases', [TestCaseController::class, 'store']);
        Route::get('test-cases/{testCase:code}', [TestCaseController::class, 'show']);
        Route::put('test-cases/{testCase:code}/hide', [TestCaseController::class, 'hide']);

        Route::get('testers', [TesterController::class, 'index']);
        Route::post('testers', [TesterController::class, 'store']);
        Route::post('testers/{tester:code}/reset-code', [TesterController::class, 'resetCode']);
        Route::put('testers/{tester:code}', [TesterController::class, 'update']);

        Route::post('test-cases/import/preview', [TestCaseImportController::class, 'preview']);
        Route::post('test-cases/import', [TestCaseImportController::class, 'store']);


    });

});
