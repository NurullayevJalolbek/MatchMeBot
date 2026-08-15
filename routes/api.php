<?php

use App\Http\Controllers\Api\BoostController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Telegram Bot Webhook & Management Routes (API prefix)
Route::prefix('telegram')->group(function () {
    Route::post('/webhook', [TelegramBotController::class, 'handleWebhook'])->name('api.telegram.webhook');
    Route::get('/set-webhook', [TelegramBotController::class, 'setWebhook'])->name('api.telegram.set_webhook');
    Route::get('/delete-webhook', [TelegramBotController::class, 'deleteWebhook'])->name('api.telegram.delete_webhook');
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo'])->name('api.telegram.webhook_info');
});

// Mini-App Onboarding API Routes
Route::prefix('onboarding')->middleware(['locale'])->group(function () {
    Route::post('/init', [OnboardingController::class, 'init'])->name('api.onboarding.init');
    Route::post('/terms', [OnboardingController::class, 'acceptTerms'])->name('api.onboarding.terms');
    Route::post('/step', [OnboardingController::class, 'saveStep'])->name('api.onboarding.step');
    Route::post('/upload-photo', [OnboardingController::class, 'uploadPhoto'])->name('api.onboarding.upload_photo');
    Route::post('/delete-photo', [OnboardingController::class, 'deletePhoto'])->name('api.onboarding.delete_photo');
});

// Discovery & Filter API Routes
Route::prefix('discovery')->middleware(['locale'])->group(function () {
    Route::get('/filter', [DiscoveryController::class, 'getFilter'])->name('api.discovery.get_filter');
    Route::post('/filter', [DiscoveryController::class, 'saveFilter'])->name('api.discovery.save_filter');
});

// Wallet & Deposit API Routes
Route::prefix('wallet')->middleware(['locale'])->group(function () {
    Route::get('/balance', [WalletController::class, 'getBalance'])->name('api.wallet.get_balance');
    Route::post('/deposit', [WalletController::class, 'submitDeposit'])->name('api.wallet.deposit');
});

// Boost API Routes
Route::prefix('boost')->middleware(['locale'])->group(function () {
    Route::get('/status', [BoostController::class, 'getStatus'])->name('api.boost.status');
    Route::post('/activate', [BoostController::class, 'activate'])->name('api.boost.activate');
});

// Likes & VIP Gifts API Routes
Route::prefix('likes')->middleware(['locale'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\LikesController::class, 'getLikes'])->name('api.likes.index');
    Route::post('/accept', [\App\Http\Controllers\Api\LikesController::class, 'accept'])->name('api.likes.accept');
    Route::post('/reject', [\App\Http\Controllers\Api\LikesController::class, 'reject'])->name('api.likes.reject');
});
