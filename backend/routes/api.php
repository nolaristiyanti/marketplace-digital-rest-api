<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// WAJIB pakai token dan only admin can manage categories
Route::middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
    Route::post('/categories', [ProductCategoryController::class, 'store']);
    Route::put('/categories/{category}', [ProductCategoryController::class, 'update']);
    Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
});

// WAJIB pakai token dan only can be accessed by role : seller
Route::middleware(['auth:sanctum', 'ensure.seller'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('ensure.owner');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('ensure.owner');
});

Route::prefix('auth')->group(function () {
    //Public Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    //WAJIB pakai token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

//Public Routes
Route::get('/categories', [ProductCategoryController::class, 'index']);
Route::get('/categories/{category}', [ProductCategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/sellers/product-count', [ProductController::class, 'productCountPerSeller']);
Route::get('/transactions/detail', [ProductController::class, 'transactionDetail']);
