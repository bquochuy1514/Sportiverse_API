<?php

use App\Http\Controllers\API\DiscountController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
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

        // // Admin order routes
        Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
        Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // Admin discount routes
        Route::get('/admin/discounts', [DiscountController::class, 'index']);
        Route::post('/admin/discounts', [DiscountController::class, 'store']);
        Route::get('/admin/discounts/{discount}', [DiscountController::class, 'show']);
        Route::put('/admin/discounts/{discount}', [DiscountController::class, 'update']);
        Route::delete('/admin/discounts/{discount}', [DiscountController::class, 'destroy']);
        Route::delete('/admin/orders/{order}/remove-discount', [DiscountController::class, 'removeFromOrder']);
    });

    // Cart routes - cần đăng nhập
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cart}', [CartController::class, 'update']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
    // Route::delete('/cart', [CartController::class, 'clear']);

    // Order routes - cần đăng nhập
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);


    // Kiểm tra mã giảm giá
    Route::post('/discounts/verify', [DiscountController::class, 'verify']);
    // Áp dụng mã giảm giá vào đơn hàng
    Route::post('/orders/{order}/apply-discount', [DiscountController::class, 'applyToOrder']);
    // Hủy áp dụng mã giảm giá
    Route::delete('/orders/{order}/remove-discount', [DiscountController::class, 'removeFromOrder']);
});






