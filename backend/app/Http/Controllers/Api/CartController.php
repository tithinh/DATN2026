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

        // Lấy user_id từ header hoặc query param (cho guest = 1)
        $guestUserId = $request->header('X-Guest-User-Id') ?? $request->query('guest_user_id');

        if ($user) {
            // User đã đăng nhập: lấy theo user_id
            $items = CartItem::with(['product', 'variant'])
                ->where('user_id', $user->id)
                ->get();
        } elseif ($guestUserId) {
            // Guest với user_id = 1: lấy theo user_id
            $items = CartItem::with(['product', 'variant'])
                ->where('user_id', $guestUserId)
                ->get();
        } else {
            // Fallback: lấy theo session_id
            $sessionId = $request->session()->getId();
            $items = CartItem::with(['product', 'variant'])
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get();
        }

        $subtotal = $items->sum(function ($item) {
            $price = $item->variant ? $item->variant->final_price : $item->product->base_price;
            return $price * $item->quantity;
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
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);

        // Kiểm tra tồn kho
        if ($validated['quantity'] > $variant->stock) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 422);
        }

        // Xác định user_id
        $user = Auth::guard('sanctum')->user();
        $guestUserId = $request->header('X-Guest-User-Id') ?? $request->query('guest_user_id');

        // Sử dụng user_id từ auth hoặc guest (mặc định = 1)
        $userId = $user ? $user->id : ($guestUserId ?? 1);

        CartItem::create([
            'user_id'    => $userId,
            'product_id' => $variant->product_id,
            'variant_id' => $validated['variant_id'],
            'quantity'   => $validated['quantity'],
            'price'      => $variant->price,
        ]);

        return response()->json(['message' => 'Đã thêm vào giỏ hàng']);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'         => 'required|exists:cart_items,id',
            'quantity'   => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,variant_id',
        ]);

        $cartItem = CartItem::findOrFail($validated['id']);

        // Cập nhật số lượng và variant_id (nếu gửi)
        $cartItem->update([
            'quantity'   => $validated['quantity'],
            'variant_id' => $validated['variant_id'] ?? $cartItem->variant_id,
        ]);

        // Tùy chọn: reload item với quan hệ để trả về thông tin đầy đủ
        $cartItem->load(['variant', 'variant.product']);

        return response()->json([
            'message' => 'Cập nhật số lượng thành công',
            'item'    => $cartItem,
        ]);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ
     */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);

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
            CartItem::where('user_id', $user->id)->delete();
        } else {
            CartItem::where('session_id', $request->session()->getId())
                    ->whereNull('user_id')
                    ->delete();
        }

        return response()->json(['message' => 'Đã xóa toàn bộ giỏ hàng']);
    }

    /**
     * Tính tổng số lượng và giá trị giỏ hàng
     */
    public function total(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $query = CartItem::query();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $request->session()->getId())
                  ->whereNull('user_id');
        }

        $totalQuantity = $query->sum('quantity');
        $totalPrice = $query->get()->sum(function ($item) {
            $price = $item->variant ? $item->variant->final_price : $item->product->base_price;
            return $price * $item->quantity;
        });

        return response()->json([
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);
    }
}
