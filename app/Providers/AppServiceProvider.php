<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\AssetService;
use App\Services\SupplyService;
use App\Services\RisService;
use App\Services\BarcodeService;
use App\Services\TransactionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(AssetService::class, function ($app) {
            return new AssetService();
        });

        $this->app->singleton(SupplyService::class, function ($app) {
            return new SupplyService();
        });

        $this->app->singleton(RisService::class, function ($app) {
            return new RisService();
        });

        $this->app->singleton(BarcodeService::class, function ($app) {
            return new BarcodeService();
        });

        $this->app->singleton(TransactionService::class, function ($app) {
            return new TransactionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
