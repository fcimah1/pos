<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebLoginController;

Route::get('/login', [WebLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebLoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/logout', [WebLoginController::class, 'logout']);
    
    Route::get('/{any}', function () {
        return view('welcome');
    })->where('any', '.*');
});
