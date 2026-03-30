<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebLoginController;
use App\Http\Controllers\AdminAuthController;

// POST /login is kept to allow the WebLoginController (Blade form) to work
// if needed, but GET /login is removed so the Vue SPA handles it.
Route::get('/login', [WebLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebLoginController::class, 'login']);
Route::get('/logout', [WebLoginController::class, 'logout']);

// Admin authentication routes (POST only - Vue handles the GET)
Route::post('/admin/auth', [AdminAuthController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// All GET routes go to Vue SPA — Auth is handled by Vue Router + Sanctum token
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
