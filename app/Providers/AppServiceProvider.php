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
        $this->app->singleton(iWalletService::class, WalletService::class);
        $this->app->singleton(iBoostService::class, BoostService::class);
        $this->app->singleton(iLikesService::class, LikesService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
