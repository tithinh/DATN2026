<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    /**
     * Public endpoint — no auth required.
     * Accepts order_code OR phone, returns order with items.
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_code' => 'nullable|string|max:50',
            'phone'      => 'nullable|string|max:20',
        ]);

        $orderCode = trim($request->input('order_code', ''));
        $phone     = trim($request->input('phone', ''));

        if (empty($orderCode) && empty($phone)) {
            return response()->json([
                'message' => 'Vui lòng nhập mã đơn hàng hoặc số điện thoại.',
            ], 422);
        }

        $query = Order::with(['items.product', 'items.variant']);

        if (!empty($orderCode)) {
            $query->where('order_code', $orderCode);
        } else {
            $query->where('customer_phone', $phone);
        }

        $order = $query->orderBy('created_at', 'desc')->first();

        if (!$order) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng. Vui lòng kiểm tra lại thông tin.',
            ], 404);
        }

        return response()->json([
            'order' => [
                'order_code'       => $order->order_code,
                'status'           => $order->status,
                'status_text'      => $this->getStatusText($order->status),
                'status_progress'  => $this->getStatusProgress($order->status),
                'customer_name'    => $order->customer_name,
                'customer_phone'   => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'payment_method'   => $order->payment_method,
                'total_amount'     => $order->total_amount,
                'discount_amount'  => $order->discount_amount,
                'final_amount'     => $order->final_amount,
                'created_at'       => $order->created_at->format('H:i, d/m/Y'),
                'items'            => $order->items->map(function ($item) {
                    $product = $item->product ?? $item->variant?->product;
                    return [
                        'name'     => $product?->name ?? 'Sản phẩm',
                        'variant'  => $item->variant
                            ? trim($item->variant->color . ' ' . $item->variant->storage_size)
                            : null,
                        'quantity' => $item->quantity,
                        'price'    => $item->price_at_purchase,
                        'subtotal' => $item->price_at_purchase * $item->quantity,
                        'image'    => $product?->thumbnail
                            ? '/storage/' . ltrim($product->thumbnail, '/')
                            : null,
                    ];
                }),
            ],
        ]);
    }

    private function getStatusText(string $status): string
    {
        return [
            'pending'    => 'Chờ xác nhận',
            'confirmed'  => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping'   => 'Đang giao hàng',
            'delivered'  => 'Đã giao hàng',
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Đã hủy',
        ][$status] ?? $status;
    }

    private function getStatusProgress(string $status): int
    {
        return [
            'pending'    => 20,
            'confirmed'  => 40,
            'processing' => 55,
            'shipping'   => 75,
            'delivered'  => 90,
            'completed'  => 100,
            'cancelled'  => 0,
        ][$status] ?? 0;
    }
}
