<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Guest customer info
            'customer_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        DB::beginTransaction();

        try {
            // Guest luôn dùng user_id = 1, không tin vào header
            $userId = Auth::check() ? Auth::id() : 1;

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

            // Override với thông tin từ request nếu là guest (khách chưa đăng nhập)
            if (!Auth::check() && $request->has('customer_name')) {
                $customerName = $request->customer_name;
                $customerPhone = $request->phone ?? '';
                $customerEmail = $request->email ?? 'guest@fivetech.vn';
                $customerAddress = $request->shipping_address ?? '';
            }

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

                // Tính giá đúng: lấy từ product (discount_price hoặc base_price) + variant price_extra
                $product = $cartItem->product;
                $variant = $cartItem->variant;

                if (!$product) {
                    throw new \Exception('Không tìm thấy sản phẩm trong giỏ hàng.');
                }

                // Giá gốc = discount_price nếu có, không thì base_price
                $unitPrice = $product->discount_price ?? $product->base_price;

                // Cộng thêm price_extra từ variant nếu có
                if ($variant && $variant->price_extra > 0) {
                    $unitPrice += $variant->price_extra;
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
                    'price_at_purchase' => $unitPrice,
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
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($promo) {
                    // Kiểm tra số lượt sử dụng
                    if (!$promo->max_uses || $promo->used_count < $promo->max_uses) {
                        // Kiểm tra đơn tối thiểu
                        if ($subtotal >= ($promo->min_order_amount ?? 0)) {
                            $promoId = $promo->promo_id;

                            // Tính giảm giá dựa theo promo_type (đúng tên cột DB)
                            if ($promo->promo_type === 'percentage') {
                                $discountAmount = $subtotal * ($promo->discount_value / 100);
                            } elseif ($promo->promo_type === 'fixed') {
                                $discountAmount = $promo->discount_value;
                            }

                            // Không giảm quá subtotal
                            $discountAmount = min($discountAmount, $subtotal);

                            // Tăng số lần sử dụng
                            $promo->increment('used_count');
                        }
                    }
                }
            }

            // Tính phí ship server-side — không tin giá trị từ client
            // Miễn phí nếu subtotal >= 300.000đ
            $shippingFee = $subtotal >= 300000 ? 0 : 30000;
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
                // Customer info
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'customer_address' => $customerAddress,
            ]);

            foreach ($orderItemsData as $data) {
                $order->items()->create([
                    'product_id' => $data['product_id'],
                    'variant_id' => $data['variant_id'],
                    'quantity' => $data['quantity'],
                    'price_at_purchase' => $data['price_at_purchase'],
                ]);

                // Trừ stock của variant
                if ($data['variant_id']) {
                    ProductVariant::where('variant_id', $data['variant_id'])
                        ->decrement('stock', $data['quantity']);
                }
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
            ->with(['items.product', 'items.variant.product'])
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
                    $product = $item->product ?? $item->variant?->product;
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id ?? $item->variant?->product_id,
                        'name' => $product?->name ?? 'Sản phẩm',
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
            return in_array($order['status'], ['pending', 'shipping']);
        })->values();

        $historyOrders = $orders->filter(function ($order) {
            return in_array($order['status'], ['completed']);
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
        $order = Order::where(function ($query) use ($orderIdentifier) {
            $query->where('order_id', $orderIdentifier)
                ->orWhere('order_code', $orderIdentifier);
        })
            ->with(['items.product', 'items.variant.product', 'user'])
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
                $product = $item->product ?? $item->variant?->product;
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id ?? $item->variant?->product_id,
                    'name' => $product?->name ?? 'Sản phẩm',
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
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Chỉ có thể hủy đơn hàng đang chờ xử lý',
            ], 400);
        }

        // Hoàn lại stock cho các variant khi hủy đơn
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                ProductVariant::where('variant_id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            }
        }

        // Xóa đơn hàng thay vì đánh dấu cancelled
        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => 'Đã hủy và xóa đơn hàng thành công',
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
                'message' => 'Đơn hàng không trong trạng thái đang giao',
            ], 400);
        }

        $order->update(['status' => 'completed']);

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
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
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
            // Ưu tiên lấy từ order fields, fallback sang user
            $customerName = $order->customer_name ?: ($order->user ? $order->user->full_name : 'Khách vãng lai');
            $customerPhone = $order->customer_phone ?: ($order->user ? $order->user->phone : '');
            $customerEmail = $order->customer_email ?: ($order->user ? $order->user->email : '');

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
                'shipping_address' => $order->shipping_address ?: $order->customer_address,
                'note' => $order->note,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'customer_name' => $customerName,
                'phone' => $customerPhone,
                'email' => $customerEmail,
            ];
        });

        return response()->json($orders);
    }

    /**
     * Chi tiết đơn hàng cho admin (có đầy đủ thông tin sản phẩm)
     */
    public function adminShow($order_id)
    {
        $order = Order::where('order_id', $order_id)
            ->with(['items.product', 'items.variant.product', 'user'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        // Customer info - ưu tiên từ order, fallback sang user
        $customerName = $order->customer_name ?: ($order->user ? $order->user->full_name : 'Khách vãng lai');
        $customerPhone = $order->customer_phone ?: ($order->user ? $order->user->phone : '');
        $customerEmail = $order->customer_email ?: ($order->user ? $order->user->email : '');

        return response()->json([
            'id' => $order->order_id,
            'order_code' => $order->order_code,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'status_text' => $this->getStatusText($order->status),
            'total_amount' => $order->total_amount,
            'discount_amount' => $order->discount_amount ?? 0,
            'final_amount' => $order->final_amount ?? $order->total_amount,
            'payment_method' => $order->payment_method,
            'note' => $order->note,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'customer_name' => $customerName,
            'phone' => $customerPhone,
            'email' => $customerEmail,
            'shipping_address' => $order->shipping_address ?: $order->customer_address,
            'items' => $order->items->map(function ($item) {
                // Fallback: lấy product qua variant nếu product_id null (đơn hàng cũ)
                $product = $item->product ?? $item->variant?->product;
                return [
                    'product_id' => $item->product_id ?? $item->variant?->product_id,
                    'product_name' => $product?->name ?? 'Sản phẩm',
                    'variant_id' => $item->variant_id,
                    'variant_name' => $item->variant ? ($item->variant->color . ($item->variant->storage_size ? ' - ' . $item->variant->storage_size : '')) : null,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng (admin)
     */
    public function updateStatus(Request $request, $order_id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,shipping,completed',
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
            'pending' => 'Chờ xử lý',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn thành',
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * Lấy progress % cho status
     */
    private function getStatusProgress($status)
    {
        $progress = [
            'pending' => 33,
            'shipping' => 66,
            'completed' => 100,
        ];
        return $progress[$status] ?? 0;
    }
}
