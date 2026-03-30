<?php

namespace App\Providers;

use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\DepartmentRepository;
use App\Repository\DesignationRepository;
use App\Repository\ExpenseRepository;
use App\Repository\Interface\CategoryRepositoryInterface;
use App\Repository\Interface\CustomerRepositoryInterface;
use App\Repository\Interface\DepartmentRepositoryInterface;
use App\Repository\Interface\DesignationRepositoryInterface;
use App\Repository\Interface\ExpenseRepositoryInterface;
use App\Repository\Interface\OrderRepositoryInterface;
use App\Repository\Interface\PaymentRepositoryInterface;
use App\Repository\Interface\ProductRepositoryInterface;
use App\Repository\Interface\ShiftRepositoryInterface;
use App\Repository\Interface\TableRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ShiftRepository;
use App\Repository\TableRepository;
use App\Repository\UserRepository;
use App\Repository\DeliveryPersonRepository;
use App\Repository\Interface\DeliveryPersonRepositoryInterface;
use App\Repository\SettingRepository;
use App\Repository\Interface\SettingRepositoryInterface;
use App\Repository\BranchRepository;
use App\Repository\Interface\BranchRepositoryInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->singleton(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->singleton(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->singleton(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->singleton(TableRepositoryInterface::class, TableRepository::class);
        $this->app->singleton(DesignationRepositoryInterface::class, DesignationRepository::class);
        $this->app->singleton(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->singleton(DeliveryPersonRepositoryInterface::class, DeliveryPersonRepository::class);
        $this->app->singleton(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->singleton(BranchRepositoryInterface::class, BranchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            error_log("[DB] " . $query->sql);
        });
    }
}
