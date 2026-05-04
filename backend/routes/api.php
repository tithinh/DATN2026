<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TrackOrderController;

Route::prefix('v1')->group(function () {

    // ======================
    // PUBLIC ROUTES
    // ======================

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/search', [ProductController::class, 'search']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    // Promotions
    Route::get('/promotions/active', [PromotionController::class, 'active']);
    Route::get('/promotions/available', [PromotionController::class, 'available']);
    Route::post('/promotions/check', [PromotionController::class, 'checkCode']);

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);

    // Social Login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook']);
    Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

    // Password Reset
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    // News (public)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);
    Route::get('/news/categories', [NewsController::class, 'categories']);
    Route::get('/news/popular', [NewsController::class, 'popular']);

    // Contact
    Route::post('/contacts', [ContactController::class, 'store']);

    // Cart (guest + user, no auth required)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update', [CartController::class, 'update']);
        Route::delete('/remove/{cartItemId}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::get('/total', [CartController::class, 'total']);
    });

    // Order (public creation + public view for guest + tracking)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderIdentifier}', [OrderController::class, 'show']);
    Route::post('/orders/track', [OrderController::class, 'sendTrackingLink']);
    Route::post('/track-order', [TrackOrderController::class, 'track']);

    // Payment confirmation (public for webhook, protected for user/admin)
    Route::post('/orders/{orderIdentifier}/confirm-payment', [OrderController::class, 'updatePaymentStatus']);


    // ======================
    // PROTECTED ROUTES (khách hàng đã đăng nhập)
    // ======================
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('/user', [AuthController::class, 'me']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::put('/user/password', [AuthController::class, 'updatePassword']);
        Route::post('/user/avatar', [AuthController::class, 'updateAvatar']);

        // Addresses
        Route::prefix('addresses')->group(function () {
            Route::get('/', [UserAddressController::class, 'index']);
            Route::post('/', [UserAddressController::class, 'store']);
            Route::put('/{id}', [UserAddressController::class, 'update']);
            Route::delete('/{id}', [UserAddressController::class, 'destroy']);
            Route::post('/{id}/default', [UserAddressController::class, 'setDefault']);
        });

        // Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/add/{product_id}', [WishlistController::class, 'add']);
            Route::delete('/remove/{product_id}', [WishlistController::class, 'remove']);
            Route::delete('/clear', [WishlistController::class, 'clear']);
        });

        // Orders (user)
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/{order_id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{order_id}/confirm-received', [OrderController::class, 'confirmReceived']);
        });

        // Comments
        Route::post('/products/{product_id}/comments', [CommentController::class, 'store']);
        Route::get('/products/{product_id}/rating', [CommentController::class, 'userRating']);
        Route::prefix('comments')->group(function () {
            Route::post('/{comment_id}/reply', [CommentController::class, 'reply']);
            Route::put('/{comment_id}', [CommentController::class, 'update']);
            Route::delete('/{comment_id}', [CommentController::class, 'destroy']);
            Route::post('/{comment_id}/like', [CommentController::class, 'like']);
            Route::post('/{comment_id}/unlike', [CommentController::class, 'unlike']);
        });
    });

    // ======================
    // ADMIN ROUTES (phải là Admin model, không phải User thường)
    // ======================
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user_id}', [UserController::class, 'show']);
            Route::put('/{user_id}', [UserController::class, 'update']);
            Route::put('/{user_id}/toggle-status', [UserController::class, 'toggleStatus']);
            Route::delete('/{user_id}', [UserController::class, 'destroy']);
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'adminIndex']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/{category_id}', [CategoryController::class, 'show']);
            Route::put('/{category_id}', [CategoryController::class, 'update']);
            Route::put('/{category_id}/toggle-status', [CategoryController::class, 'toggleStatus']);
            Route::delete('/{category_id}', [CategoryController::class, 'destroy']);
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'adminIndex']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/{product_id}', [ProductController::class, 'show']);
            Route::put('/{product_id}', [ProductController::class, 'update']);
            Route::put('/{product_id}/toggle-visibility', [ProductController::class, 'toggleVisibility']);
            Route::delete('/{product_id}', [ProductController::class, 'destroy']);
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'adminIndex']);
            Route::get('/{order_id}', [OrderController::class, 'adminShow']);
            Route::put('/{order_id}/status', [OrderController::class, 'updateStatus']);
            Route::delete('/{order_id}', [OrderController::class, 'adminDestroy']);
        });

        Route::get('/comments', [CommentController::class, 'adminIndex']);
        Route::post('/comments/{comment_id}/reply', [CommentController::class, 'reply']);

        Route::prefix('promotions')->group(function () {
            Route::get('/', [PromotionController::class, 'adminIndex']);
            Route::post('/', [PromotionController::class, 'store']);
            Route::get('/{promotion_id}', [PromotionController::class, 'show']);
            Route::put('/{promotion_id}', [PromotionController::class, 'update']);
            Route::put('/{promotion_id}/toggle-status', [PromotionController::class, 'toggleStatus']);
            Route::delete('/{promotion_id}', [PromotionController::class, 'destroy']);
        });

        Route::prefix('news')->group(function () {
            Route::get('/', [NewsController::class, 'adminIndex']);
            Route::post('/', [NewsController::class, 'store']);
            Route::get('/{id}', [NewsController::class, 'adminShow']);
            Route::put('/{id}', [NewsController::class, 'update']);
            Route::delete('/{id}', [NewsController::class, 'destroy']);
            Route::put('/{id}/toggle-status', [NewsController::class, 'toggleStatus']);
        });

        Route::prefix('contacts')->group(function () {
            Route::get('/', [ContactController::class, 'adminIndex']);
            Route::get('/{id}', [ContactController::class, 'adminShow']);
            Route::put('/{id}/status', [ContactController::class, 'updateStatus']);
            Route::delete('/{id}', [ContactController::class, 'destroy']);
            Route::put('/{id}/spam', [ContactController::class, 'markAsSpam']);
        });

        Route::prefix('admins')->group(function () {
            Route::get('/', [AdminController::class, 'index']);
            Route::post('/', [AdminController::class, 'store']);
            Route::get('/{admin_id}', [AdminController::class, 'show']);
            Route::put('/{admin_id}', [AdminController::class, 'update']);
            Route::put('/{admin_id}/toggle-status', [AdminController::class, 'toggleStatus']);
            Route::delete('/{admin_id}', [AdminController::class, 'destroy']);
        });
    });
});

