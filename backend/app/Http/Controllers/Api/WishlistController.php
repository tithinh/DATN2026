<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Lấy danh sách sản phẩm yêu thích của user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['product' => function ($query) {
                $query->select('product_id', 'name', 'slug', 'base_price', 'discount_price')
                    ->with(['variants' => function ($q) {
                        $q->select('variant_id', 'product_id', 'color', 'storage_size', 'image_urls', 'stock')
                            ->where('stock', '>', 0)
                            ->orderBy('color');
                    }]);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform data
        $wishlists = $wishlists->map(function ($item) {
            $product = $item->product;
            if (!$product) return null;

            // Lấy hình ảnh từ variant đầu tiên
            $image = null;
            $variants = $product->variants ?? [];
            if ($variants->isNotEmpty()) {
                $firstVariant = $variants->first();
                $imageUrls = $firstVariant->image_urls;
                if (is_string($imageUrls)) {
                    $imageUrls = json_decode($imageUrls, true);
                }
                if (!empty($imageUrls) && isset($imageUrls[0])) {
                    $image = '/storage/' . $imageUrls[0];
                }
            }

            return [
                'wishlist_id' => $item->wishlist_id,
                'product_id' => $product->product_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'base_price' => $product->base_price,
                'discount_price' => $product->discount_price,
                'final_price' => $product->discount_price ?? $product->base_price,
                'image' => $image,
                'variants' => $variants->map(function ($v) {
                    $imageUrls = $v->image_urls;
                    if (is_string($imageUrls)) {
                        $imageUrls = json_decode($imageUrls, true);
                    }
                    return [
                        'variant_id' => $v->variant_id,
                        'color' => $v->color,
                        'storage_size' => $v->storage_size,
                        'image' => !empty($imageUrls[0]) ? '/storage/' . $imageUrls[0] : null,
                        'stock' => $v->stock,
                    ];
                }),
                'created_at' => $item->created_at,
            ];
        })->filter();

        return response()->json([
            'data' => $wishlists->values(),
            'total' => $wishlists->count(),
        ]);
    }

    /**
     * Thêm sản phẩm vào wishlist
     */
    public function add(Request $request, $product_id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để thêm vào danh sách yêu thích',
            ], 401);
        }

        // Kiểm tra sản phẩm tồn tại
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        // Kiểm tra đã có trong wishlist chưa
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Sản phẩm đã có trong danh sách yêu thích',
            ], 400);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
        ]);

        return response()->json([
            'message' => 'Đã thêm vào danh sách yêu thích',
            'success' => true,
        ]);
    }

    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function remove(Request $request, $product_id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Đã xóa khỏi danh sách yêu thích',
                'success' => true,
            ]);
        }

        return response()->json([
            'message' => 'Sản phẩm không có trong danh sách yêu thích',
        ], 404);
    }

    /**
     * Xóa tất cả sản phẩm trong wishlist
     */
    public function clear(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Đã xóa tất cả sản phẩm yêu thích',
            'success' => true,
        ]);
    }
}
