<?php

use Illuminate\Support\Facades\Route;
use Iluminate\Http\Request;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;

Route::apiResource('/categories', ProductCategoryController::class);
Route::apiResource('/products', ProductController::class);

//WAJIB pakai token
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

//Public Routes (tanpa auth)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
