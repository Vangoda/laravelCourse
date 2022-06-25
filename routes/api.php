<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AmbassadorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Users are divided into groups. Groups are used to limit the access to API.
// Some API calls are shared between all groups and will be grouped in a 
// function. Access to API is done by using laravel scopes.

/**
 * Common endpoints for scopes
 * @param string $scope 
 * @return void 
 */
function common(string $scope){
    // Common routes shared by groups
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum', 'scope.'.$scope )->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::put('users/info', [AuthController::class, 'updateInfo']);
        Route::put('users/password', [AuthController::class, 'updatePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
}

// Admin group
Route::prefix('admin')->group(function () {
    // The routes
    common('admin');

    // Admin protected routes
    Route::middleware('auth:sanctum', 'scope.admin' )->group(function () {
        Route::get('ambassadors', [AmbassadorController::class, 'index']);
        Route::get('users/{id}/links', [LinkController::class, 'index']);
        Route::get('orders', [OrderController::class, 'index']);
    
        /* apiResource creates following routes
        Verb          Path                              Action  Route Name
        GET           /products                         index   products.index
        POST          /products                         store   products.store
        GET           /products/{product}               show    products.show
        PUT|PATCH     /products/{product}               update  products.update
        DELETE        /products/{product}               destroy products.destroy
        */
        Route::apiResource('products', ProductController::class);
    });
});

// Ambassador group
Route::prefix('ambassador')->group(function () {
    // Ambassador routes
    common('ambassador');

    // Ambassador specific
    Route::get('products/frontend', [ProductController::class, 'frontend']);
    Route::get('products/backend', [ProductController::class, 'backend']);

    // Ambassador protected
    Route::middleware('auth:sanctum', 'scope.ambassador' )->group(function () {
        // Stats
        Route::get('stats', [StatsController::class, 'index']);
        Route::get('rankings', [StatsController::class, 'rankings']);

        // Links, only ambassadors can create links
        Route::post('links', [LinkController::class, 'store']);
    });
});

// Checkout
Route::prefix('checkout')->group(function() {
    Route::get('link/{code}', [LinkController::class, 'show']);
});