<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;
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

Route::post('register', [Api\AuthenticationController::class, 'register']);
Route::post('login', [Api\AuthenticationController::class, 'login']);

Route::get('products', [Api\ProductController::class, 'index']);
Route::get('categories', [Api\CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/update', [Api\AuthenticationController::class, 'update']);
    Route::get('user', [Api\AuthenticationController::class, 'me']);
    Route::post('logout', [Api\AuthenticationController::class, 'logout']);

    Route::get('orders', [Api\OrderController::class, 'index']);
    Route::get('orders/{order}', [Api\OrderController::class, 'show']);
    Route::post('orders', [Api\OrderController::class, 'store']);

    Route::get('carts', [Api\CartController::class, 'getCartItems']);
    Route::post('carts', [Api\CartController::class, 'addToCart']);
    Route::post('carts/remove', [Api\CartController::class, 'removeFromCart']);
});

Route::post('midtrans-callback', [Api\OrderController::class, 'handleCallback']);
