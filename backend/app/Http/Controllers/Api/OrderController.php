<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ProductVariant; // Nếu cần kiểm tra stock

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng mới (hỗ trợ cả user đăng nhập và guest vãng lai)
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.cart_item_id' => 'required|exists:cart_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,variant_id',

            'customer_info' => 'required|array',
            'customer_info.name' => 'required|string|max:255',
            'customer_info.phone' => 'required|string|max:20',
            'customer_info.email' => 'required|email',
            'customer_info.address' => 'required|string|max:255',
            'customer_info.city' => 'required|string|max:100',
            'customer_info.district' => 'required|string|max:100',

            'shipping_method' => 'required|string|in:standard,express,same-day',
            'payment_method' => 'required|string|in:cod,bank,momo,vnpay',
            'note' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Bắt đầu transaction để đảm bảo dữ liệu nhất quán
        DB::beginTransaction();

        try {
            // Xác định user_id
            $userId = Auth::check() ? Auth::id() : null;

            if (!$userId) {
                // Tìm hoặc tạo user guest
                $guest = User::firstOrCreate(
                    ['email' => 'guest_' . Str::random(8) . '@fivetech.vn'],
                    [
                        'full_name' => $validated['customer_info']['name'],
                        'phone' => $validated['customer_info']['phone'],
                        'address' => $validated['customer_info']['address'],
                        'password' => bcrypt(Str::random(16)),
                        'is_active' => false, // Không cho login
                    ]
                );
                $userId = $guest->id;
            }

            // Tính tổng tiền lại ở backend (an toàn)
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $cartItem = CartItem::findOrFail($itemData['cart_item_id']);

                // Kiểm tra quyền sở hữu cart item
                if ($cartItem->user_id !== $userId && $cartItem->session_id !== session()->getId()) {
                    throw new \Exception('Không có quyền sử dụng sản phẩm này trong giỏ hàng.');
                }

                // Lấy giá thực tế (ưu tiên variant price_extra nếu có)
                $unitPrice = $cartItem->price;
                if ($cartItem->variant_id && $itemData['variant_id']) {
                    $variant = ProductVariant::find($itemData['variant_id']);
                    if ($variant && $variant->price_extra > 0) {
                        $unitPrice += $variant->price_extra;
                    }
                }

                // Kiểm tra tồn kho
                $stock = $cartItem->variant ? $cartItem->variant->stock : 9999; // fallback nếu không có variant
                if ($itemData['quantity'] > $stock) {
                    throw new \Exception("Sản phẩm '{$cartItem->product->name}' chỉ còn {$stock} trong kho.");
                }

                $subtotal += $unitPrice * $itemData['quantity'];

                $orderItemsData[] = [
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $itemData['variant_id'] ?? $cartItem->variant_id,
                    'quantity' => $itemData['quantity'],
                    'price' => $unitPrice,
                ];
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $userId,
                'total' => $subtotal - ($request->discount ?? 0) + ($request->shipping_fee ?? 0),
                'status' => 'pending',
                'shipping_method' => $validated['shipping_method'],
                'payment_method' => $validated['payment_method'],
                'note' => $validated['note'],
                // Thông tin khách (lưu riêng cho guest hoặc đồng bộ)
                'customer_name' => $validated['customer_info']['name'],
                'customer_phone' => $validated['customer_info']['phone'],
                'customer_email' => $validated['customer_info']['email'],
                'customer_address' => $validated['customer_info']['address'],
                'customer_city' => $validated['customer_info']['city'],
                'customer_district' => $validated['customer_info']['district'],
            ]);

            // Tạo order items
            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            // Xóa giỏ hàng
            if (Auth::check()) {
                CartItem::where('user_id', Auth::id())->delete();
            } else {
                CartItem::where('session_id', session()->getId())->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'order_id' => $order->id,
                'total' => $order->total,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Đặt hàng thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
