<?php

use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\OnboardingController;
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
