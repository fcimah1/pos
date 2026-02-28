<?php

namespace App\Providers;

use App\Repository\Interface\OrderRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 
            Schema::defaultStringLength(191);

    }
}
