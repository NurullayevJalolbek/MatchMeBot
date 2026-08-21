<?php

namespace App\Providers;

use App\Contracts\iBoostService;
use App\Contracts\iDiscoveryService;
use App\Contracts\iLikesService;
use App\Contracts\iOnboardingService;
use App\Contracts\iTelegramBotService;
use App\Contracts\iTelegramUserService;
use App\Contracts\iWalletService;
use App\Services\BoostService;
use App\Services\DiscoveryService;
use App\Services\LikesService;
use App\Services\OnboardingService;
use App\Services\TelegramBotService;
use App\Services\TelegramUserService;
use App\Services\WalletService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(iTelegramBotService::class, TelegramBotService::class);
        $this->app->singleton(iTelegramUserService::class, TelegramUserService::class);
        $this->app->singleton(iOnboardingService::class, OnboardingService::class);
        $this->app->singleton(iDiscoveryService::class, DiscoveryService::class);
        $this->app->singleton(iBoostService::class, BoostService::class);
        $this->app->singleton(iLikesService::class, LikesService::class);
        $this->app->singleton(\App\Contracts\iSubscriptionService::class, \App\Services\SubscriptionService::class);
        $this->app->singleton(\App\Contracts\iSubscriptionFeatureService::class, \App\Services\SubscriptionFeatureService::class);
        $this->app->singleton(\App\Contracts\iAdminUserService::class, \App\Services\AdminUserService::class);
        $this->app->singleton(\App\Contracts\iExpenseCategoryService::class, \App\Services\ExpenseCategoryService::class);
        $this->app->singleton(\App\Contracts\iIncomeCategoryService::class, \App\Services\IncomeCategoryService::class);
        $this->app->singleton(\App\Contracts\iExpenseService::class, \App\Services\ExpenseService::class);
        $this->app->singleton(\App\Contracts\iPaymentService::class, \App\Services\PaymentService::class);
        $this->app->singleton(\App\Contracts\iProfileOptionService::class, \App\Services\ProfileOptionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }
}
