<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SwipeController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify', [AuthController::class, 'verifyOtp']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/onboarding', [AuthController::class, 'onboard']);
    Route::get('/me', [ProfileController::class, 'me']);
    Route::patch('/me/safety', [ProfileController::class, 'updateSafety']);
    Route::post('/reports', [ProfileController::class, 'report']);
    Route::post('/blocks', [ProfileController::class, 'block']);

    Route::get('/discovery', [DiscoveryController::class, 'index']);
    Route::post('/swipes', [SwipeController::class, 'store']);
    Route::get('/matches/{match}/messages', [MessageController::class, 'index']);
    Route::post('/matches/{match}/messages', [MessageController::class, 'store']);
    Route::post('/matches/{match}/calls', [CallController::class, 'start']);
    Route::post('/calls/{call}/end', [CallController::class, 'end']);

    Route::get('/wallet/packages', [WalletController::class, 'packages']);
    Route::get('/wallet/history', [WalletController::class, 'history']);
    Route::post('/wallet/orders', [WalletController::class, 'createOrder']);
    Route::post('/wallet/verify', [WalletController::class, 'verifyPayment']);
});
