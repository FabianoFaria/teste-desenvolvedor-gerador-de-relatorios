<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
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

    // demais rotas protegidas (reports) entram aqui depois
});
