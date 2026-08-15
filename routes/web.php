<?php

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

// Admin Panel Views
Route::get('/admin', function () {
    return view('admin.pages.dashboard.index');
})->name('admin.dashboard');

Route::get('/login', function () {
    return view('admin.auth.login');
})->name('login');

// Telegram Webhook Routes (Fallback & Standard)
Route::prefix('telegram')->group(function () {
    Route::post('/webhook', [TelegramBotController::class, 'handleWebhook'])->name('telegram.webhook');
    Route::get('/set-webhook', [TelegramBotController::class, 'setWebhook'])->name('telegram.set_webhook');
    Route::get('/delete-webhook', [TelegramBotController::class, 'deleteWebhook'])->name('telegram.delete_webhook');
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo'])->name('telegram.webhook_info');
});
