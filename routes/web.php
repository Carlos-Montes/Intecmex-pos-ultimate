<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ConnectController as ApiConnectController;


Route::middleware('guest')->group(function(){
    Route::prefix('connect')->group(function(){
        Route::get('/login', [ConnectController::class, 'getLogin'])->name('connect.login');
    });
});

/**  SYSTEM AUTH  */
Route::middleware('auth')->group(function(){
    Route::get('/', [DashboardController::class, 'getHome'])->name('dashboard');
    Route::get('/logout', [ConnectController::class, 'getLogout'])->name('logout');
});


Route::prefix('api-js')->group(function(){
    Route::post('/connect/login', [ApiConnectController::class, 'postLogin'])->name('api.connect.login');
});