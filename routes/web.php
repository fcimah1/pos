<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebLoginController;

// POST /login is kept to allow the WebLoginController (Blade form) to work
// if needed, but GET /login is removed so the Vue SPA handles it.
Route::post('/login', [WebLoginController::class, 'login']);
Route::get('/logout', [WebLoginController::class, 'logout']);

// All GET routes go to Vue SPA — Auth is handled by Vue Router + Sanctum token
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
