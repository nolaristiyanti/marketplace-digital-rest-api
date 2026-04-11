<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;

// Route::apiResource('/categories', ProductCategoryController::class);
// Route::apiResource('/products', ProductController::class);

//WAJIB pakai token
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // only can be accessed by role : seller
    Route::middleware('ensure.seller')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('ensure.owner');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('ensure.owner');
    });

    // Route::post('/products', [ProductController::class, 'store']);
    // Route::put('/products/{id}', [ProductController::class, 'update']);
    // Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // only can be accessed by role : seller
    Route::middleware('ensure.admin')->group(function () {
        Route::post('/categories', [ProductCategoryController::class, 'store']);
        Route::put('/categories/{category}', [ProductCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy']);
    });

    // Route::post('/categories', [ProductCategoryController::class, 'store']);
    // Route::put('/categories/{id}', [ProductCategoryController::class, 'update']);
    // Route::delete('/categories/{id}', [ProductCategoryController::class, 'destroy']);
});

//Public Routes (tanpa auth)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [ProductCategoryController::class, 'index']);
Route::get('/categories/{category}', [ProductCategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
