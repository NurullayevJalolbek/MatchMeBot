<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\Route;

// Telegram Bot Mini-App Views
Route::get('/', function () {
    return view('telegram_bot.mini_app.index');
})->name('home');

Route::get('/app', function () {
    return view('telegram_bot.mini_app.index');
})->name('app');

Route::get('/discovery', function () {
    return view('telegram_bot.mini_app.pages.discovery');
})->name('discovery');

Route::get('/likes', function () {
    return view('telegram_bot.mini_app.pages.likes');
})->name('likes');

Route::get('/roulette', function () {
    return view('telegram_bot.mini_app.pages.roulette');
})->name('roulette');

// Admin Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.pages.dashboard.index');
    })->name('dashboard');

    // Boost Management Routes
    Route::resource('boosts', \App\Http\Controllers\Admin\BoostController::class);
    Route::post('boosts/{boost}/toggle-status', [\App\Http\Controllers\Admin\BoostController::class, 'toggleStatus'])->name('boosts.toggle');

    // Subscription Plans Management Routes
    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class);
    Route::post('subscriptions/{subscription}/toggle-status', [\App\Http\Controllers\Admin\SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');

    // Subscription Features Management Routes
    Route::resource('subscription-features', \App\Http\Controllers\Admin\SubscriptionFeatureController::class);
    Route::post('subscription-features/{subscription_feature}/toggle-status', [\App\Http\Controllers\Admin\SubscriptionFeatureController::class, 'toggleStatus'])->name('subscription-features.toggle');
});

// Telegram Webhook Routes (Fallback & Standard)
Route::prefix('telegram')->group(function () {
    Route::post('/webhook', [TelegramBotController::class, 'handleWebhook'])->name('telegram.webhook');
    Route::get('/set-webhook', [TelegramBotController::class, 'setWebhook'])->name('telegram.set_webhook');
    Route::get('/delete-webhook', [TelegramBotController::class, 'deleteWebhook'])->name('telegram.delete_webhook');
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo'])->name('telegram.webhook_info');
});
