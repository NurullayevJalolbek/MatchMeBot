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

    // Administrators Management Routes
    Route::resource('admins', \App\Http\Controllers\Admin\AdminUserController::class);
    Route::post('admins/{admin}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('admins.toggle');

    // Expense Categories Management Routes
    Route::resource('expense-categories', \App\Http\Controllers\Admin\ExpenseCategoryController::class);
    Route::post('expense-categories/{expense_category}/toggle-status', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'toggleStatus'])->name('expense-categories.toggle');

    // Income Categories Management Routes
    Route::resource('income-categories', \App\Http\Controllers\Admin\IncomeCategoryController::class);
    Route::post('income-categories/{income_category}/toggle-status', [\App\Http\Controllers\Admin\IncomeCategoryController::class, 'toggleStatus'])->name('income-categories.toggle');

    // Expenses Management Routes
    Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);
    Route::post('expenses/{expense}/toggle-status', [\App\Http\Controllers\Admin\ExpenseController::class, 'toggleStatus'])->name('expenses.toggle');

    // Payments (Tushumlar) Management Routes
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/approve', [\App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
    Route::delete('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->name('payments.destroy');

    // Xizmatlar Tarixi (User Subscriptions & Boosts History) Routes
    Route::get('user-subscriptions', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'index'])->name('user-subscriptions.index');
    Route::post('user-subscriptions/{user_subscription}/cancel', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'cancel'])->name('user-subscriptions.cancel');
    Route::delete('user-subscriptions/{user_subscription}', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'destroy'])->name('user-subscriptions.destroy');

    Route::get('user-boosts', [\App\Http\Controllers\Admin\UserBoostController::class, 'index'])->name('user-boosts.index');
    Route::post('user-boosts/{user_boost}/cancel', [\App\Http\Controllers\Admin\UserBoostController::class, 'cancel'])->name('user-boosts.cancel');
    Route::delete('user-boosts/{user_boost}', [\App\Http\Controllers\Admin\UserBoostController::class, 'destroy'])->name('user-boosts.destroy');

    // Ma'lumotnomalar (Profile Options) Management Routes
    Route::resource('profile-options', \App\Http\Controllers\Admin\ProfileOptionController::class);
    Route::post('profile-options/{profile_option}/toggle-status', [\App\Http\Controllers\Admin\ProfileOptionController::class, 'toggleStatus'])->name('profile-options.toggle');
});
