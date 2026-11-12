<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestCaseController;
use App\Http\Controllers\Api\TestResulController;
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
        Route::post('test-cases/{testCase:code}/result', [TestResulController::class, 'store']);
        Route::post('submit', [TestResulController::class, 'submit']);
    });
});
