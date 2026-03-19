<?php

use Illuminate\Support\Facades\Route;
use Iluminate\Http\Request;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;

Route::apiResource('/categories', ProductCategoryController::class);
Route::apiResource('/products', ProductController::class);
