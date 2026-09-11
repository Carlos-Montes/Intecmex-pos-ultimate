<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FinancesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\ConnectController as ApiConnectController;
use App\Http\Controllers\Api\AccountController as ApiAccountController;
use App\Http\Controllers\Api\SettingsController as ApiSettingsController;
use App\Http\Controllers\Api\CategoriesController as ApiCategoriesController;
use App\Http\Controllers\Api\FinancesController as ApiFinancesController;


Route::middleware('guest')->group(function(){
    Route::prefix('connect')->group(function(){
        Route::get('/login', [ConnectController::class, 'getLogin'])->name('connect.login');
    });
});

/**  SYSTEM AUTH  */
Route::middleware('auth')->group(function(){
    Route::prefix('connect')->group(function(){
        Route::get('/logout', [ConnectController::class, 'getLogout'])->name('logout');
    });

    Route::get('/', [DashboardController::class, 'getHome'])->name('dashboard');

    Route::prefix('profile')->group(function(){
        Route::get('/edit', [AccountController::class, 'getProfileEdit'])->name('dashboard');
    });

    Route::prefix('categories')->group(function(){
        Route::get('/list', [CategoriesController::class, 'getCategories'])->name('categories');
        Route::get('/{id}/subs', [CategoriesController::class, 'getSubCategories'])->name('subcategories');
    });

    Route::prefix('inventory')->group(function(){
        Route::get('/initial', [InventoryController::class, 'getInventory'])->name('inventory');
    });

    Route::prefix('finances')->group(function(){
        Route::get('/list/{type}', [FinancesController::class, 'getFinances'])->name('finances');
    });

    Route::prefix('settings')->group(function(){
        Route::get('/', [SettingsController::class, 'getSettings'])->name('settings');
        Route::post('/',[ApiSettingsController::class, 'postSettings'])->name('settings');
        Route::get('/clear',[ApiSettingsController::class, 'getSettingsClear'])->name('platform_settings_clear');
    });

});


Route::prefix('api-js')->group(function(){
    Route::post('/connect/login', [ApiConnectController::class, 'postLogin'])->name('api.connect.login');
    Route::post('/account/update', [ApiAccountController::class, 'postProfileUpdate'])->name('api.account.update');
    Route::post('/categories/add', [ApiCategoriesController::class, 'postCategoriesAdd'])->name('api.categories.add');
    Route::post('/categorie/{id}/edit', [ApiCategoriesController::class, 'postCategoriesEdit'])->name('api.categories.edit');
    Route::get('/categorie/{id}/delete', [ApiCategoriesController::class, 'getCategoriesDelete'])->name('api.categories.delete');
    Route::get('/subcategorie/{id}/delete', [ApiCategoriesController::class, 'getSubcategoriesDelete'])->name('api.subcategories.delete');
    Route::post('/accounts/add', [ApiFinancesController::class, 'postAccountsAdd'])->name('api.accounts.add');
    Route::post('/account/{id}/edit', [ApiFinancesController::class, 'postAccountsEdit'])->name('api.accounts.edit');
    Route::get('/account/{id}/delete', [ApiFinancesController::class, 'getAccountsDelete'])->name('api.accounts.delete');
    Route::post('/expenses/add', [ApiFinancesController::class, 'postExpensesAdd'])->name('api.expenses.add');
});