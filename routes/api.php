<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SportController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public sport routes - ai cũng có thể xem
Route::get('/sports', [SportController::class, 'index']);
Route::get('/sports/{sport}', [SportController::class, 'show']);

// Public category routes - ai cũng có thể xem
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
// Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

// Public product routes - ai cũng có thể xem
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Admin routes - chỉ admin mới có quyền tạo, sửa, xóa
    Route::middleware('admin')->group(function () {
        // Sports
        Route::post('/sports', [SportController::class, 'store']);
        Route::put('/sports/{sport}', [SportController::class, 'update']);
        Route::delete('/sports/{sport}', [SportController::class, 'destroy']);
    
        // Admin category routes - chỉ admin mới có quyền tạo, sửa, xóa
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Admin product routes - chỉ admin mới có quyền tạo, sửa, xóa
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });
});





// Route::apiResource('products', ProductController::class);

// Route::apiResource('categories', CategoryController::class);





