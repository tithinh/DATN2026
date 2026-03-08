<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ProductVariant;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.cart_item_id' => 'required|exists:cart_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,variant_id',
            'payment_method' => 'required|string|in:cod',
            'shipping_address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            // Guest user_id from header or query param (for guest with user_id = 1)
            $guestUserId = $request->header('X-Guest-User-Id') ?? $request->query('guest_user_id');
            $userId = Auth::check() ? Auth::id() : ($guestUserId ?? 1);

            // Lấy thông tin user từ bảng users
            $user = User::find($userId);

            // Nếu không tìm thấy user, sử dụng user_id = 1
            if (!$user) {
                $user = User::find(1);
            }

            // Nếu vẫn không có user, lấy thông tin từ request (fallback)
            $customerName = $user ? $user->full_name : 'Guest';
            $customerPhone = $user ? $user->phone : '';
            $customerEmail = $user ? $user->email : 'guest@fivetech.vn';
            $customerAddress = $user ? $user->address : '';

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $cartItem = CartItem::findOrFail($itemData['cart_item_id']);

                // Cho phep user_id = 1 (guest) su dung cart items
                $isGuestUser = ($userId == 1);

                if ($isGuestUser) {
                    if ($cartItem->user_id != 1 && $cartItem->user_id != null) {
                        throw new \Exception('Khong co quyen su dung san pham nay trong gio hang.');
                    }
                } else {
                    if ($cartItem->user_id != $userId && $cartItem->session_id != session()->getId()) {
                        throw new \Exception('Khong co quyen su dung san pham nay trong gio hang.');
                    }
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

            // Tạo mã đơn hàng tự động
            $orderCode = $this->generateOrderCode();

            // Tìm promo từ coupon_code nếu có
            $promoId = null;
            $discountAmount = 0;
            if (!empty($validated['coupon_code'])) {
                $promo = \App\Models\Promotion::where('code', $validated['coupon_code'])
                    ->where('is_active', true)
                    ->first();
                if ($promo) {
                    $promoId = $promo->id;
                    // Tính giảm giá
                    if ($promo->discount_type === 'percentage') {
                        $discountAmount = $subtotal * ($promo->discount_value / 100);
                    } else {
                        $discountAmount = $promo->discount_value;
                    }
                }
            }

            // Tính toán các khoản
            $shippingFee = $request->shipping_fee ?? 0;
            $totalAmount = $subtotal - $discountAmount + $shippingFee;

            $order = Order::create([
                'user_id' => $userId,
                'order_code' => $orderCode,
                'promo_id' => $promoId,
                'discount_amount' => $discountAmount,
                'final_amount' => $totalAmount,
                'total_amount' => $subtotal,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'shipping_address' => $validated['shipping_address'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($orderItemsData as $data) {
                $order->items()->create($data);
            }

            if (Auth::check()) {
                CartItem::where('user_id', Auth::id())->delete();
            } else {
                CartItem::where('session_id', session()->getId())->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'order_code' => $order->order_code,
                'order_id' => $order->order_id,
                'total' => $order->total_amount,
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
                'order_code' => $order->order_code,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'total' => $order->total_amount,
                'payment_method' => $order->payment_method,
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
    public function show(Request $request, $orderIdentifier)
    {
        // Chấp nhận cả order_id và order_code
        $order = Order::where(function($query) use ($orderIdentifier) {
                $query->where('order_id', $orderIdentifier)
                      ->orWhere('order_code', $orderIdentifier);
            })
            ->with(['items.product', 'items.variant', 'user'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        // Nếu có user đăng nhập, kiểm tra quyền sở hữu
        $user = $request->user();
        if ($user && $order->user_id != $user->id) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        $orderData = [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'status' => $order->status,
            'status_text' => $this->getStatusText($order->status),
            'total_amount' => $order->total_amount,
            'discount_amount' => $order->discount_amount ?? 0,
            'final_amount' => $order->final_amount ?? $order->total_amount,
            'promo_id' => $order->promo_id,
            'payment_method' => $order->payment_method,
            'note' => $order->note,
            'created_at' => $order->created_at,
            'progress' => $this->getStatusProgress($order->status),
            'shipping' => [
                'name' => $order->user ? $order->user->full_name : 'Khách hàng',
                'phone' => $order->user ? $order->user->phone : '',
                'address' => $order->shipping_address ?? ($order->user ? $order->user->address : ''),
            ],
            'customer' => [
                'name' => $order->user ? $order->user->full_name : 'Khách hàng',
                'email' => $order->user ? $order->user->email : '',
                'phone' => $order->user ? $order->user->phone : '',
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

        $order = Order::where('order_id', $order_id)
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

        $order = Order::where('order_id', $order_id)
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
     * Danh sách đơn hàng cho admin
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user']);

        // Search by order_code or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 15;
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform data to match frontend expectations
        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->order_id,
                'order_code' => $order->order_code,
                'user_id' => $order->user_id,
                'promo_id' => $order->promo_id,
                'total_amount' => $order->total_amount,
                'discount_amount' => $order->discount_amount ?? 0,
                'final_amount' => $order->final_amount ?? $order->total_amount,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'payment_method' => $order->payment_method,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'customer_name' => $order->user ? $order->user->full_name : 'Khách vãng lai',
                'phone' => $order->user ? $order->user->phone : '',
                'email' => $order->user ? $order->user->email : '',
            ];
        });

        return response()->json($orders);
    }

    /**
     * Cập nhật trạng thái đơn hàng (admin)
     */
    public function updateStatus(Request $request, $order_id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipping,delivered,completed,cancelled',
        ]);

        $order = Order::where('order_id', $order_id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'success' => true,
            'order' => [
                'id' => $order->order_id,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
            ],
        ]);
    }

    /**
     * Tạo mã đơn hàng tự động: FT-YYYYMMDD-XXXX
     */
    private function generateOrderCode()
    {
        $today = now()->format('Ymd');
        $prefix = 'FT-' . $today . '-';

        // Lấy đơn hàng cuối cùng trong ngày
        $lastOrder = Order::where('order_code', 'like', $prefix . '%')
            ->orderBy('order_code', 'desc')
            ->first();

        if ($lastOrder) {
            // Lấy số cuối và tăng lên 1
            $lastNumber = (int) substr($lastOrder->order_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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
