<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\DeliveryPersonController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\BranchController;

Route::post('/login', [AuthController::class, 'login']);

// Routes محمية بـ Sanctum للـ mobile app
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/barcode/{barcode}', [ProductController::class, 'byBarcode']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Routes للـ POS
Route::middleware(['web'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::apiResource('categories', CategoryController::class);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/suspended', [OrderController::class, 'suspended']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/settle', [OrderController::class, 'settleOrders']);
    Route::post('/orders/{id}', [OrderController::class, 'update']);

    Route::get('/tables', [TableController::class, 'index']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/orders/{orderId}/payments', [PaymentController::class, 'getOrderPayments']);

    Route::get('/customers/search', [CustomerController::class, 'search']);
    Route::apiResource('customers', CustomerController::class);

    Route::post('/customer-addresses', [CustomerAddressController::class, 'store']);
    Route::patch('/customer-addresses/{id}', [CustomerAddressController::class, 'update']);
    Route::delete('/customer-addresses/{id}', [CustomerAddressController::class, 'destroy']);

    Route::apiResource('delivery-persons', DeliveryPersonController::class);

    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/daily/pdf', [ReportController::class, 'dailyPdf']);
    Route::get('/reports/products', [ReportController::class, 'products']);
    Route::get('/reports/products/pdf', [ReportController::class, 'productsPdf']);

    Route::post('/shifts/open', [ShiftController::class, 'openShift']);
    Route::post('/shifts/close', [ShiftController::class, 'closeShift']);
    Route::get('/shifts/current', [ShiftController::class, 'getCurrentShift']);
    Route::get('/shifts/by-date', [ShiftController::class, 'byDate']);
    Route::get('/reports/shift/pdf', [ReportController::class, 'shiftPdf']);

    // المصروفات
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::patch('/expenses/{id}', [ExpenseController::class, 'update']);

    // الموظفون والنظام الهرمي
    Route::get('/visible-employees', [EmployeeController::class, 'visible']);
    Route::get('/employee-backups/{userId}', [EmployeeController::class, 'backups']);
    Route::apiResource('employees', EmployeeController::class);

    // إعدادات الشركة والفروع
    Route::get('/company/settings', [CompanyController::class, 'index']);
    Route::post('/company/settings', [CompanyController::class, 'update']);
    Route::apiResource('branches', BranchController::class);
});
