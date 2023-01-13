<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\CategoryController;
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\CartController;
use \App\Http\Controllers\OrderController;

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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::get('categories', [CategoryController::class, 'getCategories']);
Route::get('products', [ProductController::class, 'getProducts']);
Route::get('products/{slug}', [ProductController::class, 'getProductBySlug']);

Route::post('cart/add', [CartController::class, 'store']);
Route::post('/cart/delete', [CartController::class, 'destroy']);
Route::post('cart/delete/all', [CartController::class, 'destroyAll']);

Route::post('product/{id}/buy', [OrderController::class, 'buyProduct']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('order/list', [OrderController::class, 'getMyOrderList']);
});

