<?php

use App\Http\Controllers\AmbassadorController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Admin group
Route::prefix('admin')->group(function () {
    // The route
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum', 'scope.admin')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::put('users/info', [AuthController::class, 'updateInfo']);
        Route::put('users/password', [AuthController::class, 'updatePassword']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('ambassadors', [AmbassadorController::class, 'index']);
    });
});

// Ambassador


// Checkout
