<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Seller\ProductController;
use App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\Seller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('seller')->name('seller.')->group(function () {
    Route::post('register', [Seller\AuthenticationController::class, 'register']);
    Route::post('login', [Seller\AuthenticationController::class, 'login']);

    Route::middleware('auth:sanctum,seller')->group(function () {
        Route::post('logout', [Seller\AuthenticationController::class, 'logout']);
        Route::get('me', [Seller\AuthenticationController::class, 'me']);

        Route::apiResource('products', Seller\ProductController::class);
    });
});

Route::prefix('user')->name('user.')->group(function () {
    Route::post('register', [User\AuthenticationController::class, 'register']);
    Route::post('login', [User\AuthenticationController::class, 'login']);

    Route::middleware('auth:sanctum,user')->group(function () {
        Route::post('logout', [User\AuthenticationController::class, 'logout']);
        Route::get('me', [User\AuthenticationController::class, 'me']);

        Route::get('products', [User\ProductController::class, 'index']);

        Route::post('orders', [User\OrderController::class, 'store']);
    });
});
