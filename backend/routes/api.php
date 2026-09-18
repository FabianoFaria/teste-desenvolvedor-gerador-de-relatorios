<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BillingReportController;
use App\Http\Controllers\BillingReportExportController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('customers', CustomerController::class)->only([
        'index', 'show', 'store', 'update',
    ]);

    Route::apiResource('billings', BillingController::class)->only([
        'index', 'show', 'store', 'update',
    ]);
    Route::post('billings/{billing}/pay', [BillingController::class, 'pay']);

    Route::get('reports/billing', [BillingReportController::class, 'billing']);
    Route::get('reports/billing/export/csv', [BillingReportExportController::class, 'csv']);
    Route::get('reports/billing/export/pdf', [BillingReportExportController::class, 'pdf']);
});
