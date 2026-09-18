<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FinancesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\Api\ConnectController as ApiConnectController;
use App\Http\Controllers\Api\AccountController as ApiAccountController;
use App\Http\Controllers\Api\SettingsController as ApiSettingsController;
use App\Http\Controllers\Api\CategoriesController as ApiCategoriesController;
use App\Http\Controllers\Api\UsersController as ApiUsersController;


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

    Route::prefix('products')->group(function(){
        Route::get('/{status}', [ProductsController::class, 'getProductos'])->name('product');
    });

    Route::prefix('settings')->group(function(){
        Route::get('/', [SettingsController::class, 'getSettings'])->name('settings');
        Route::post('/',[ApiSettingsController::class, 'postSettings'])->name('settings');
        Route::get('/clear',[ApiSettingsController::class, 'getSettingsClear'])->name('platform_settings_clear');
    });

    Route::prefix('users')->group(function(){
        Route::get('/list/{type}', [UsersController::class, 'getUsers'])->name('users_list');
        Route::get('/{id}/view', [UsersController::class, 'getUsersViews'])->name('users_views');
        Route::get('/{id}/permissions', [UsersController::class, 'getUsersPermissions'])->name('users_permissions');
    });
});


Route::prefix('api-js')->group(function(){
    Route::post('/connect/login', [ApiConnectController::class, 'postLogin'])->name('api.connect.login');
    Route::post('/account/update', [ApiAccountController::class, 'postProfileUpdate'])->name('api.account.update');
    Route::post('/categories/add', [ApiCategoriesController::class, 'postCategoriesAdd'])->name('api.categories.add');
    Route::post('/categorie/{id}/edit', [ApiCategoriesController::class, 'postCategoriesEdit'])->name('api.categories.edit');
    Route::post('/form_user_edit_info_{id}', [ApiUsersController::class, 'postUserEditData'])->name('api.users.edit_data');
    Route::post('/form_user_edit_password_{id}', [ApiUsersController::class, 'postUserEditPassword'])->name('api.users.edit.password');
    Route::post('/form_permissions_users_{id}', [ApiUsersController::class, 'postUserEditPermissions'])->name('api.users.edit.permissions');
    Route::get('/form_inactive_user_{id}/inactive', [ApiUsersController::class, 'getUserInactive'])->name('api.users.inactive');
    Route::get('/categorie/{id}/delete', [ApiCategoriesController::class, 'getCategoriesDelete'])->name('api.categories.delete');
    Route::get('/subcategorie/{id}/delete', [ApiCategoriesController::class, 'getSubcategoriesDelete'])->name('api.subcategories.delete');

});