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

// Tất cả route trong api.php đã có prefix /api tự động

Route::prefix('v1')->group(function () {
    // ======================
    // PUBLIC ROUTES (không cần auth)
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
    Route::post('/promotions/check', [PromotionController::class, 'checkCode']);

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    Route::post('/admin/login', [AuthController::class, 'adminLogin']);

    // News (public)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);
    Route::get('/news/categories', [NewsController::class, 'categories']);
    Route::get('/news/popular', [NewsController::class, 'popular']);

    // Contact (public - gửi liên hệ)
    Route::post('/contacts', [ContactController::class, 'store']);

    // CSRF cho Sanctum (nếu cần)
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });

    // ======================
    // PROTECTED ROUTES (yêu cầu đăng nhập Sanctum - cho khách hàng)
    // ======================
    Route::middleware('auth:sanctum')->group(function () {

        // User / Profile (cho khách hàng đăng nhập)
        Route::get('/user', [AuthController::class, 'me']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::put('/user/password', [AuthController::class, 'updatePassword']);
        Route::post('/user/avatar', [AuthController::class, 'updateAvatar']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // Cart
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::put('/update', [CartController::class, 'update']);
            Route::delete('/remove/{id}', [CartController::class, 'remove']);
            Route::delete('/clear', [CartController::class, 'clear']);
            Route::get('/total', [CartController::class, 'total']);
        });

        // Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/add/{product_id}', [WishlistController::class, 'add']);
            Route::delete('/remove/{product_id}', [WishlistController::class, 'remove']);
            Route::delete('/clear', [WishlistController::class, 'clear']);
        });

        // Orders (cho user)
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::post('/{order_id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{order_id}/confirm-received', [OrderController::class, 'confirmReceived']);
            Route::post('/checkout/validate', [OrderController::class, 'validateCheckout']);
        });

        // Comments / Reviews
        Route::prefix('products/{product_id}/comments')->group(function () {
            Route::post('/', [CommentController::class, 'store']);
        });

        Route::prefix('comments')->group(function () {
            Route::post('/{comment_id}/reply', [CommentController::class, 'reply']);
            Route::put('/{comment_id}', [CommentController::class, 'update']);
            Route::delete('/{comment_id}', [CommentController::class, 'destroy']);
            Route::post('/{comment_id}/like', [CommentController::class, 'like']);
            Route::post('/{comment_id}/unlike', [CommentController::class, 'unlike']);
        });
    });

    // Guest Cart (không cần đăng nhập)
    Route::prefix('guest/cart')->group(function () {
        Route::get('/', [CartController::class, 'guestIndex']);
        Route::post('/add', [CartController::class, 'addGuest']);
        Route::put('/update', [CartController::class, 'updateGuest']);
        Route::delete('/remove/{variant_id}', [CartController::class, 'removeGuest']);
        Route::delete('/clear', [CartController::class, 'clearGuest']);
    });

    // PUBLIC ORDER CREATION (cho cả guest và user)
    Route::post('/orders', [OrderController::class, 'store']);

    // PUBLIC ORDER VIEW (cho guest xem đơn hàng sau khi đặt)
    Route::get('/orders/{orderIdentifier}', [OrderController::class, 'show']);



    // ======================
    // ADMIN ROUTES (bây giờ nằm trong v1, yêu cầu auth:sanctum)
    // ======================
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {


        // Quản lý người dùng (khách hàng)
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);               // Danh sách user
            Route::get('/{user_id}', [UserController::class, 'show']);      // Chi tiết user
            Route::put('/{user_id}', [UserController::class, 'update']);    // Sửa user
            Route::put('/{user_id}/toggle-status', [UserController::class, 'toggleStatus']); // Ẩn/hiện
        });

        // Quản lý danh mục
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'adminIndex']);     // Danh sách cho admin
            Route::post('/', [CategoryController::class, 'store']);         // Thêm danh mục
            Route::get('/{category_id}', [CategoryController::class, 'show']);
            Route::put('/{category_id}', [CategoryController::class, 'update']);
            Route::put('/{category_id}/toggle-status', [CategoryController::class, 'toggleStatus']);
        });

        // Quản lý sản phẩm
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'adminIndex']);      // Danh sách cho admin
            Route::post('/', [ProductController::class, 'store']);          // Thêm sản phẩm
            Route::get('/{product_id}', [ProductController::class, 'show']);
            Route::put('/{product_id}', [ProductController::class, 'update']);
            Route::put('/{product_id}/toggle-visibility', [ProductController::class, 'toggleVisibility']);
            Route::delete('/{product_id}', [ProductController::class, 'destroy']); // Nếu cần xóa
        });

        // Quản lý đơn hàng
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'adminIndex']);        // Danh sách đơn hàng
            Route::get('/{order_id}', [OrderController::class, 'show']);
            Route::put('/{order_id}/status', [OrderController::class, 'updateStatus']); // Cập nhật trạng thái
        });

        // Quản lý bình luận
        Route::prefix('comments')->group(function () {
            Route::get('/', [CommentController::class, 'adminIndex']);      // Danh sách bình luận cho admin
            Route::put('/{comment_id}/approve', [CommentController::class, 'approve']);
            Route::delete('/{comment_id}', [CommentController::class, 'destroy']);
        });

        // Quản lý khuyến mãi
        Route::prefix('promotions')->group(function () {
            Route::get('/', [PromotionController::class, 'adminIndex']);     // Danh
            Route::post('/', [PromotionController::class, 'store']);         // Thêm khuyến mãi
            Route::get('/{promotion_id}', [PromotionController::class, 'show']);
            Route::put('/{promotion_id}', [PromotionController::class, 'update']);
            Route::put('/{promotion_id}/toggle-status', [PromotionController::class, 'toggleStatus']);
        });

        // Quản lý tin tức
        Route::prefix('news')->group(function () {
            Route::get('/', [NewsController::class, 'adminIndex']);          // Danh sách
            Route::post('/', [NewsController::class, 'store']);              // Thêm bài viết
            Route::get('/{id}', [NewsController::class, 'adminShow']);        // Chi tiết
            Route::put('/{id}', [NewsController::class, 'update']);          // Cập nhật
            Route::delete('/{id}', [NewsController::class, 'destroy']);      // Xóa
            Route::put('/{id}/toggle-status', [NewsController::class, 'toggleStatus']); // Toggle status
        });

        // Quản lý liên hệ
        Route::prefix('contacts')->group(function () {
            Route::get('/', [ContactController::class, 'adminIndex']);       // Danh sách
            Route::get('/{id}', [ContactController::class, 'adminShow']);    // Chi tiết
            Route::put('/{id}/status', [ContactController::class, 'updateStatus']); // Cập nhật trạng thái
            Route::delete('/{id}', [ContactController::class, 'destroy']);   // Xóa
            Route::put('/{id}/spam', [ContactController::class, 'markAsSpam']); // Đánh dấu spam
        });
    });
});
