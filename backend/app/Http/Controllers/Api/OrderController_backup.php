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
use App\Models\ProductVariant;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng mới (hỗ trợ cả user và guest với user_id = 1)
     */
    public function store(Request $request)
    {
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
            'customer_info.district' => 'required|string|max:100',
            'shipping_method' => 'required|string|in:standard,express,same-day',
            'payment_method' => 'required|string|in:cod,bank,momo,vnpay',
            'note' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            // Guest user_id from header or query param (for guest with user_id = 1)
            $guestUserId = $request->header('X-Guest-User-Id') ?? $request->query('guest_user_id');
            $userId = Auth::check() ? Auth::id() : ($guestUserId ?? null);

            if (!$userId) {
                // Tạo guest user mới
                $guest = User::firstOrCreate(
                    ['email' => 'guest_' . Str::random(8) . '@fivetech.vn'],
                    [
                        'full_name' => $validated['customer_info']['name'],
                        'phone' => $validated['customer_info']['phone'],
                        'address' => $validated['customer_info']['address'],
                        'password' => bcrypt(Str::random(16)),
                        'is_active' => false,
                    ]
                );
                $userId = $guest->id;
            }

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $cartItem = CartItem::findOrFail($itemData['cart_item_id']);

                // Kiểm tra quyền sử dụng cart item (theo user_id hoặc session_id)
                if ($cartItem->user_id != $userId && $cartItem->session_id != session()->getId()) {
                    throw new \Exception('Không có quyền sử dụng sản phẩm này trong giỏ hàng.');
                }

                $unitPrice = $cartItem->price;
                if ($cartItem->variant_id && $itemData['variant_id']) {
                    $variant = ProductVariant::find($itemData['variant_id']);
                    if ($variant && $variant->price_extra > 0) {
                        $unitPrice += $variant->price_extra;
                    }
                }

                $stock = $cartItem->variant ? $cartItem->variant->stock : 9999;
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

            $order = Order::create([
                'user_id' => $userId,
                'total' => $subtotal - ($request->discount ?? 0) + ($request->shipping_fee ?? 0),
                'status' => 'pending',
                'shipping_method' => $validated['shipping_method'],
                'payment_method' => $validated['payment_method'],
                'note' => $validated['note'],
                'customer_name' => $validated['customer_info']['name'],
                'customer_phone' => $validated['customer_info']['phone'],
                'customer_email' => $validated['customer_info']['email'],
                'customer_address' => $validated['customer_info']['address'],
                'customer_district' => $validated['customer_info']['district'],
            ]);

            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            // Xóa cart items sau khi đặt hàng thành công
            if (Auth::check()) {
                CartItem::where('user_id', Auth::id())->delete();
            } elseif ($guestUserId) {
                CartItem::where('user_id', $guestUserId)->delete();
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

    /**
     * Lấy danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'items.variant'])
            ->orderBy('created_at', 'desc')
            ->get();

        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'shipping_method' => $order->shipping_method,
                'created_at' => $order->created_at,
                'progress' => $this->getStatusProgress($order->status),
                'items' => $order->items->map(function ($item) {
                    $image = null;
                    if ($item->variant && $item->variant->image_urls) {
                        $imageUrls = is_string($item->variant->image_urls)
                            ? json_decode($item->variant->image_urls, true)
                            : $item->variant->image_urls;
                        if (!empty($imageUrls[0])) {
                            $image = '/storage/' . $imageUrls[0];
                        }
                    }
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product->name ?? 'Sản phẩm',
                        'variant' => $item->variant
                            ? ($item->variant->color . ' ' . $item->variant->storage_size)
                            : null,
                        'image' => $image,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ];
        });

        $currentOrders = $orders->filter(function ($order) {
            return in_array($order['status'], ['pending', 'confirmed', 'processing', 'shipping']);
        })->values();

        $historyOrders = $orders->filter(function ($order) {
            return in_array($order['status'], ['delivered', 'completed', 'cancelled']);
        })->values();

        return response()->json([
            'current' => $currentOrders,
            'history' => $historyOrders,
        ]);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show(Request $request, $order_id)
    {
        $user = $request->user();

        $order = Order::where('order_id', $order_id)
            ->where('user_id', $user->id)
            ->with(['items.product', 'items.variant'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        $orderData = [
            'id' => $order->id,
            'status' => $order->status,
            'status_text' => $this->getStatusText($order->status),
            'total' => $order->total,
            'payment_method' => $order->payment_method,
            'shipping_method' => $order->shipping_method,
            'note' => $order->note,
            'created_at' => $order->created_at,
            'progress' => $this->getStatusProgress($order->status),
            'customer' => [
                'name' => $order->customer_name,
                'phone' => $order->customer_phone,
                'email' => $order->customer_email,
                'address' => $order->customer_address,
                'district' => $order->customer_district,
            ],
            'items' => $order->items->map(function ($item) {
                $image = null;
                if ($item->variant && $item->variant->image_urls) {
                    $imageUrls = is_string($item->variant->image_urls)
                        ? json_decode($item->variant->image_urls, true)
                        : $item->variant->image_urls;
                    if (!empty($imageUrls[0])) {
                        $image = '/storage/' . $imageUrls[0];
                    }
                }
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name ?? 'Sản phẩm',
                    'variant' => $item->variant
                        ? ($item->variant->color . ' ' . $item->variant->storage_size)
                        : null,
                    'image' => $image,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
        ];

        return response()->json($orderData);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $order_id)
    {
        $user = $request->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Không thể hủy đơn hàng này',
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Hủy đơn hàng thành công',
            'success' => true,
        ]);
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmReceived(Request $request, $order_id)
    {
        $user = $request->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        if ($order->status !== 'shipping') {
            return response()->json([
                'message' => 'Đơn hàng không trong trạng thái vận chuyển',
            ], 400);
        }

        $order->update(['status' => 'delivered']);

        return response()->json([
            'message' => 'Xác nhận đã nhận hàng thành công',
            'success' => true,
        ]);
    }

    /**
     * Validate checkout
     */
    public function validateCheckout(Request $request)
    {
        return response()->json(['valid' => true]);
    }

    /**
     * Lấy text hiển thị cho status
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang vận chuyển',
            'delivered' => 'Đã giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * Lấy progress % cho status
     */
    private function getStatusProgress($status)
    {
        $progress = [
            'pending' => 10,
            'confirmed' => 30,
            'processing' => 50,
            'shipping' => 70,
            'delivered' => 90,
            'completed' => 100,
            'cancelled' => 100,
        ];
        return $progress[$status] ?? 0;
    }
}
