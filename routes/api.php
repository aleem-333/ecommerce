<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
Route::get('/products/filter', [ProductController::class, 'filterProducts'])->name('api.products.filter');

/*
|--------------------------------------------------------------------------
| Cart Routes (Protected by authentication + Rate Limited)
|--------------------------------------------------------------------------
| Rate limit: 60 requests per minute per user for cart operations
*/
Route::middleware(['auth:sanctum', 'throttle:cart'])->group(function () {
    Route::post('/cart', [ProductController::class, 'addToCart'])->name('api.cart.add');
    Route::put('/cart/{cartItem}', [ProductController::class, 'updateCartItem'])->name('api.cart.update');
});
