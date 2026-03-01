<?php

namespace App\Providers;

use App\Repository\Interface\OrderRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\CategoryRepositoryInterface;
use App\Repository\Interface\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \App\Repository\Interface\OrderRepositoryInterface::class,
            \App\Repository\OrderRepository::class
        );

        $this->app->singleton(
            \App\Repository\Interface\UserRepositoryInterface::class,
            \App\Repository\UserRepository::class
        );

        $this->app->singleton(
            \App\Repository\Interface\CategoryRepositoryInterface::class,
            \App\Repository\CategoryRepository::class
        );

        $this->app->singleton(
            \App\Repository\Interface\ProductRepositoryInterface::class,
            \App\Repository\ProductRepository::class
        );

        $this->app->singleton(
            \App\Repository\Interface\CustomerRepositoryInterface::class,
            \App\Repository\CustomerRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\UserRepositoryInterface::class,
            \App\Repository\UserRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\ShiftRepositoryInterface::class,
            \App\Repository\ShiftRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\PaymentRepositoryInterface::class,
            \App\Repository\PaymentRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\TableRepositoryInterface::class,
            \App\Repository\TableRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\DesignationRepositoryInterface::class,
            \App\Repository\DesignationRepository::class
        );
        $this->app->singleton(
            \App\Repository\Interface\DepartmentRepositoryInterface::class,
            \App\Repository\DepartmentRepository::class
        );

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
