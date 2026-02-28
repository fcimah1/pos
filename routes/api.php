<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\DeliveryPersonController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TableController;

Route::post('/login', [AuthController::class, 'login']);

// Routes محمية بـ Sanctum للـ mobile app
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/barcode/{barcode}', [ProductController::class, 'byBarcode']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Routes للـ POS (بدون مصادقة مؤقتاً)
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
    Route::post('/orders/{id}', [OrderController::class, 'update']);

    Route::get('/tables', [TableController::class, 'index']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/orders/{orderId}/payments', [PaymentController::class, 'getOrderPayments']);

    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/search', [CustomerController::class, 'search']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::patch('/customers/{id}', [CustomerController::class, 'update']);

    Route::post('/customer-addresses', [CustomerAddressController::class, 'store']);
    Route::patch('/customer-addresses/{id}', [CustomerAddressController::class, 'update']);
    Route::delete('/customer-addresses/{id}', [CustomerAddressController::class, 'destroy']);

    Route::get('/delivery-persons', [DeliveryPersonController::class, 'index']);
    Route::post('/delivery-persons', [DeliveryPersonController::class, 'store']);
    Route::patch('/delivery-persons/{id}', [DeliveryPersonController::class, 'update']);

    Route::get('/reports/daily', [ReportController::class, 'daily']);

    Route::post('/shifts/open', [ShiftController::class, 'openShift']);
    Route::post('/shifts/close', [ShiftController::class, 'closeShift']);
    Route::get('/shifts/current', [ShiftController::class, 'getCurrentShift']);
});
