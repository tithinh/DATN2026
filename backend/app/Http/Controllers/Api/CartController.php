<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Lấy danh sách giỏ hàng của user hiện tại (hoặc guest với user_id = 1)
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $userId = $user ? $user->user_id : 1;

$items = CartItem::with(['product', 'variant', 'product.category'])
            ->where('user_id', $userId)
            ->get();

        $subtotal = $items->sum(function ($item) {
            $basePrice = $item->product->discount_price ?? $item->product->base_price ?? 0;
            $extra = $item->variant->price_extra ?? 0;
            return ($basePrice + $extra) * $item->quantity;
        });

        return response()->json([
            'items' => $items,
            'subtotal' => $subtotal,
            'total_items' => $items->count(),
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ (hỗ trợ cả user và guest với user_id = 1)
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,variant_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);

        if ($validated['quantity'] > $variant->stock) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 422);
        }

        $user = Auth::guard('sanctum')->user();
        $userId = $user ? $user->user_id : 1;

        // Check if item already exists
        $existingItem = CartItem::where('user_id', $userId)
            ->where('variant_id', $validated['variant_id'])
            ->first();

        if ($existingItem) {
            // Update quantity +1
            $newQuantity = $existingItem->quantity + $validated['quantity'];
            if ($newQuantity > $variant->stock) {
                return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 422);
            }
            $existingItem->update(['quantity' => $newQuantity]);
            $message = 'Đã tăng số lượng trong giỏ hàng';
        } else {
            // Create new
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $variant->product_id,
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
            ]);
            $message = 'Đã thêm vào giỏ hàng';
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,variant_id',
        ]);

        $cartItem = CartItem::findOrFail($validated['id']);

        $user = Auth::guard('sanctum')->user();
        // $ownerId = $user ? $user->getKey() : 1;
        // if ($cartItem->user_id != $ownerId) {
        //     return response()->json(['message' => 'Không có quyền cập nhật sản phẩm này'], 403);
        // }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'variant_id' => $validated['variant_id'] ?? $cartItem->variant_id,
        ]);

        $cartItem->load(['variant', 'variant.product']);

        return response()->json([
            'message' => 'Cập nhật số lượng thành công',
            'item' => $cartItem,
        ]);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ
     */
    public function remove(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);

        $user = Auth::guard('sanctum')->user();
        // $ownerId = $user ? $user->getKey() : 1;
        // if ($cartItem->user_id != $ownerId) {
        //     return response()->json(['message' => 'Không có quyền xóa sản phẩm này'], 403);
        // }

        $cartItem->delete();

        return response()->json(['message' => 'Xóa sản phẩm thành công'], 200);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            CartItem::where('user_id', $user->getKey())->delete();
        } else {
            CartItem::where('user_id', 1)->delete();
        }

        return response()->json(['message' => 'Đã xóa toàn bộ giỏ hàng']);
    }

    /**
     * Tính tổng số lượng và giá trị giỏ hàng
     */
    public function total(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $userId = $user ? $user->user_id : 1;

        $query = CartItem::with(['product', 'variant'])->where('user_id', $userId);

        $totalQuantity = $query->sum('quantity');
        $totalPrice = $query->get()->sum(function ($item) {
            $basePrice = $item->product->discount_price ?? $item->product->base_price;
            $extra = $item->variant->price_extra ?? 0;
            return ($basePrice + $extra) * $item->quantity;
        });

        return response()->json([
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);
    }
}
