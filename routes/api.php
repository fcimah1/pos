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
use App\Http\Controllers\PermissionController;

Route::get('/ping', function() { return 'pong'; })->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Routes محمية بـ Sanctum للـ mobile app
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/barcode/{barcode}', [ProductController::class, 'byBarcode']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Routes للـ POS — Bearer token via Sanctum (Vue/useApi)، مع web لجلسة/CSRF عند الحاجة
Route::middleware(['web', 'auth:sanctum'])->group(function () {

    // API Routes للصلاحيات
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'getAllPermissions'])->middleware('permission:users.read');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:users.create');
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:users.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:users.delete');
        
        Route::get('/users/{user}', [PermissionController::class, 'userPermissions'])->middleware('permission:users.read');
        Route::put('/designations/{designation}/permissions', [PermissionController::class, 'updateDesignationPermissions'])->middleware('permission:users.update');
    });

    // المنتجات - السماح بالوصول للقراءة في الـ POS
    Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.create');
    Route::put('/{id}', [ProductController::class, 'update'])->middleware('permission:products.update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware('permission:products.delete');
    });
    // الفئات - السماح بالوصول للقراءة في الـ POS
    Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store'])->middleware('permission:categories.create');
    Route::get('/{id}', [CategoryController::class, 'show'])->middleware('permission:categories.read');
    Route::put('/{id}', [CategoryController::class, 'update'])->middleware('permission:categories.update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete');
    });
    // الطلبات
    Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store'])->middleware('permission:orders.create');
    Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.read');
    Route::get('/suspended', [OrderController::class, 'suspended'])->middleware('permission:orders.read');
    Route::get('/{id}', [OrderController::class, 'show'])->middleware('permission:orders.read');
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.update');
    Route::post('/settle', [OrderController::class, 'settleOrders'])->middleware('permission:orders.process');
    Route::post('/{id}', [OrderController::class, 'update'])->middleware('permission:orders.update');
    });
    // الطاولات - السماح بالوصول في الـ POS
    Route::prefix('tables')->group(function () {
    Route::get('/', [TableController::class, 'index']);
    });
    // المدفوعات
    Route::prefix('payments')->group(function () {
    Route::post('/', [PaymentController::class, 'store'])->middleware('permission:orders.process');
    Route::get('/orders/{orderId}/payments', [PaymentController::class, 'getOrderPayments'])->middleware('permission:orders.read');
    });

    // العملاء - السماح بالوصول الأساسي في الـ POS
    Route::prefix('customers')->group(function () {
    Route::get('/search', [CustomerController::class, 'search']);
    });
    Route::apiResource('customers', CustomerController::class)->middleware([
        'index' => 'permission:customers.read',
        'store' => 'permission:customers.create',
        'show' => 'permission:customers.read',
        'update' => 'permission:customers.update',
        'destroy' => 'permission:customers.delete'
    ]);

    // عناوين العملاء
    Route::prefix('customer-addresses')->group(function () {
    Route::post('/', [CustomerAddressController::class, 'store'])->middleware('permission:customers.update');
    Route::patch('/{id}', [CustomerAddressController::class, 'update'])->middleware('permission:customers.update');
    Route::delete('/{id}', [CustomerAddressController::class, 'destroy'])->middleware('permission:customers.delete');
    });
    // مندوبي التوصيل - السماح بالوصول الأساسي في الـ POS
    Route::apiResource('delivery-persons', DeliveryPersonController::class);

    // المصروفات - السماح بالوصول الأساسي في الـ POS
    Route::prefix('expenses')->group(function () {
    Route::get('/', [ExpenseController::class, 'index']);
    Route::post('/', [ExpenseController::class, 'store'])->middleware('permission:reports.financial');
    Route::patch('/{id}', [ExpenseController::class, 'update'])->middleware('permission:reports.financial');
    });
    // التقارير
    Route::prefix('reports')->group(function () {
    Route::get('/daily', [ReportController::class, 'daily'])->middleware('permission:reports.sales');
    Route::get('/daily/pdf', [ReportController::class, 'dailyPdf'])->middleware('permission:reports.sales');
    Route::get('/products', [ReportController::class, 'products'])->middleware('permission:reports.inventory');
    Route::get('/products/pdf', [ReportController::class, 'productsPdf'])->middleware('permission:reports.inventory');
    Route::get('/shift/pdf', [ReportController::class, 'shiftPdf'])->middleware('permission:reports.sales');
    });
    // الورديات
    Route::prefix('shifts')->group(function () {
    Route::post('/open', [ShiftController::class, 'openShift'])->middleware('permission:cash.open');
    Route::post('/close', [ShiftController::class, 'closeShift'])->middleware('permission:cash.close');
    Route::get('/current', [ShiftController::class, 'getCurrentShift'])->middleware('permission:reports.sales');
    Route::get('/by-date', [ShiftController::class, 'byDate'])->middleware('permission:reports.sales');
    });
    

    // المصروفات
    Route::prefix('expenses')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->middleware('permission:reports.financial');
    Route::post('/', [ExpenseController::class, 'store'])->middleware('permission:reports.financial');
    Route::patch('/{id}', [ExpenseController::class, 'update'])->middleware('permission:reports.financial');
    });
    // الموظفون والنظام الهرمي
    Route::get('/visible-employees', [EmployeeController::class, 'visible'])->middleware('permission:users.read');
    Route::get('/employee-backups/{userId}', [EmployeeController::class, 'backups'])->middleware('permission:users.read');
    
    // مسارات إضافية لتتوافق مع الواجهة الأمامية (Permissions Page)
    Route::get('/designations', [PermissionController::class, 'getDesignations'])->middleware('permission:users.read');
    Route::get('/designations/{designation}', [PermissionController::class, 'showDesignation'])->middleware('permission:users.read');
    Route::get('/users', [EmployeeController::class, 'index'])->middleware('permission:users.read');

    Route::apiResource('employees', EmployeeController::class)->middleware([
        'index' => 'permission:users.read',
        'store' => 'permission:users.create',
        'show' => 'permission:users.read',
        'update' => 'permission:users.update',
        'destroy' => 'permission:users.delete'
    ]);

    // إعدادات الشركة والفروع
    Route::get('/company/settings', [CompanyController::class, 'index'])->middleware('permission:settings.general');
    Route::post('/company/settings', [CompanyController::class, 'update'])->middleware('permission:settings.general');
    Route::apiResource('branches', BranchController::class)->middleware([
        'index' => 'permission:branches.read',
        'store' => 'permission:branches.create',
        'show' => 'permission:branches.read',
        'update' => 'permission:branches.update',
        'destroy' => 'permission:branches.delete'
    ]);
});
