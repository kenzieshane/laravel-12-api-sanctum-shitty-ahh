<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\LoginRegisterController;

// Public routes of authtication
Route::controller(LoginRegisterController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Protected routes of product and logout
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout']);

    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
});