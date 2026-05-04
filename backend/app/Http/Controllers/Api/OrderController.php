<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderTracking;
use App\Models\CartItem;
use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ProductVariant;
use App\Events\OrderCreated;
use App\Events\OrderCompleted;

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
            'payment_method' => 'required|string|in:cod,vietqr',
'shipping_address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
            // Guest customer info
            'customer_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        DB::beginTransaction();

        try {
            // Dùng sanctum guard để check auth (route này là public nên phải chỉ rõ guard)
            $authUser = Auth::guard('sanctum')->user();
            $userId = $authUser?->user_id ?? 1;

            // Lấy thông tin customer từ request (ưu tiên request, fallback về user profile)
            $customerName = $request->customer_name ?? ($authUser?->full_name ?? 'Guest');
            $customerPhone = $request->phone ?? ($authUser?->phone ?? '');
            $customerEmail = $request->email ?? ($authUser?->email ?? 'guest@fivetech.vn');
            $customerAddress = $request->shipping_address ?? ($authUser?->address ?? '');

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $cartItem = CartItem::findOrFail($itemData['cart_item_id']);

                // TEMP: Disable strict ownership check post-cart-merge (remove after verify)
                // if ($cartItem->user_id != $userId) {
                //     throw new \Exception('Không có quyền sử dụng sản phẩm này trong giỏ hàng.');
                // }

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
                    // Kiểm tra user đã sử dụng chưa
                    if ($authUser) {
                        $alreadyUsed = Order::where('user_id', $authUser->user_id)
                            ->where('promo_id', $promo->promo_id)
                            ->exists();
                        if ($alreadyUsed) {
                            $promo = null;
                        }
                    }

                    // Kiểm tra số lượt sử dụng
                    if ($promo && (!$promo->max_uses || $promo->used_count < $promo->max_uses)) {
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
                'payment_status' => $validated['payment_method'] === 'cod' ? 'paid' : 'pending',
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

            // Only delete used cart items (don't clear entire cart)
            $usedCartItemIds = collect($validated['items'])->pluck('cart_item_id');
            CartItem::whereIn('id', $usedCartItemIds)->delete();

            DB::commit();

            // Dispatch event — listener sends confirmation email via queue
            OrderCreated::dispatch($order->fresh(['items.product', 'items.variant']));

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'order_code' => $order->order_code,
                'order_id' => $order->order_id,
                'total' => $order->total_amount,
                'payment_status' => $order->payment_status,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
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

$orders = Order::withTrashed()->where('user_id', $user->getKey())
            ->with(['items.product', 'items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $userId = $request->user()->getKey();

        // Load user's existing ratings keyed by product_id for fast lookup
        $productIds = $orders->flatMap(fn($order) => $order->items)
            ->filter(fn($item) => $item->product_id ?? $item->variant?->product_id)
            ->map(fn($item) => $item->product_id ?? $item->variant?->product_id)
            ->unique()
            ->values()
            ->toArray();

        $userRatings = Comment::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->where('rating', '>', 0)
            ->whereNull('parent_id')
            ->get()
            ->keyBy('product_id');

        $orders = $orders->map(function ($order) use ($userRatings) {
            return [
                'id' => $order->order_id,
                'order_code' => $order->order_code,
                'status' => $order->status,
                'status_text' => $this->getStatusText($order->status),
                'total' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at,
                'progress' => $this->getStatusProgress($order->status),
                'items' => $order->items->map(function ($item) use ($userRatings) {
                    $image = null;
                    if ($item->variant && $item->variant->image_urls) {
                        $imageUrls = is_string($item->variant->image_urls)
                            ? json_decode($item->variant->image_urls, true)
                            : $item->variant->image_urls;
                        if (!empty($imageUrls[0])) {
                            $image = '/' . $imageUrls[0];
                        }
                    }
                    $product = $item->product ?? $item->variant?->product;
                    $productId = $item->product_id ?? $item->variant?->product_id;
                    $userRating = $userRatings->get($productId);
                    return [
                        'id' => $item->order_item_id,
                        'product_id' => $productId,
                        'name' => $product?->name ?? 'Sản phẩm',
                        'variant' => $item->variant
                            ? ($item->variant->color . ' ' . $item->variant->storage_size)
                            : null,
                        'image' => $image,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'is_reviewed' => $userRating !== null,
                        'review' => $userRating ? [
                            'comment_id' => $userRating->comment_id,
                            'rating' => $userRating->rating,
                            'content' => $userRating->content,
                        ] : null,
                    ];
                }),
            ];
        });

        $currentOrders = $orders->filter(function ($order) {
return in_array($order['status'], ['pending', 'processing', 'shipping', 'delivered']);
        })->values();

        $historyOrders = $orders->filter(function ($order) {
            return in_array($order['status'], ['completed', 'cancelled']);
        })->values();

        return response()->json([
            'orders' => $orders->values(),
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
        $order = Order::withTrashed()->where(function ($query) use ($orderIdentifier) {
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

        if ($order->deleted_at && $order->status !== 'cancelled') {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        // Nếu có user đăng nhập, kiểm tra quyền sở hữu
        $user = $request->user();
        if ($user && $order->user_id != $user->getKey()) {
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
            'payment_status' => $order->payment_status,
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
                $product = $item->product ?? $item->variant?->product;
                // Variant image first
                if ($item->variant && $item->variant->image_urls) {
                    $imageUrls = is_string($item->variant->image_urls)
                        ? json_decode($item->variant->image_urls, true)
                        : $item->variant->image_urls;
                    if (!empty($imageUrls) && !empty($imageUrls[0])) {
                        $image = '/' . ltrim($imageUrls[0], '/');
                    }
                }
                // Product thumbnail fallback
                if (!$image && $product && $product->thumbnail) {
                    $image = '/' . ltrim($product->thumbnail, '/');
                }
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

        $order = Order::where(function ($q) use ($order_id) {
                $q->where('order_id', $order_id)
                  ->orWhere('order_code', $order_id);
            })
            ->where('user_id', $user->getKey())
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

        $order->update(['status' => 'cancelled']);
        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => 'Đã hủy đơn hàng thành công',
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
            ->where('user_id', $user->getKey())
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
     * Cập nhật payment status (admin/self/webhook)
     */
    public function updatePaymentStatus(Request $request, $orderIdentifier)
    {
        $request->validate([
            'status' => 'required|in:paid,failed'
        ]);

        $order = Order::withTrashed()->where(function ($query) use ($orderIdentifier) {
            $query->where('order_id', $orderIdentifier)
                  ->orWhere('order_code', $orderIdentifier);
        })->firstOrFail();

        if ($order->payment_method !== 'vietqr' || $order->payment_status !== 'pending') {
            return response()->json(['message' => 'Không thể cập nhật'], 400);
        }

        $order->update([
            'payment_status' => $request->status
        ]);


        return response()->json([
            'message' => 'Cập nhật thanh toán thành công',
            'order' => [
                'order_code' => $order->order_code,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
            ]
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
     * Gửi email chứa link tra cứu đơn hàng cho khách vãng lai
     */
    public function sendTrackingLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:100',
        ]);

        $email = $validated['email'];
        $since = now()->subDays(90);

        // Tìm đơn hàng có email khớp (guest orders hoặc orders của user có email này)
        $orders = Order::withTrashed()
            ->where(function ($query) use ($email, $since) {
                $query->where('customer_email', $email)
                    ->orWhereHas('user', function ($q) use ($email) {
                        $q->where('email', $email);
                    });
            })
            ->where('created_at', '>=', $since)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng nào được đặt bằng email này trong 90 ngày qua.',
            ], 404);
        }

$frontendUrl = config('frontend.url', 'http://localhost:5173');

        try {
            Mail::to($email)->send(new OrderTracking($orders, $frontendUrl));
        } catch (\Exception $e) {
            Log::error('Send tracking email failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.',
            ], 500);
        }

        return response()->json([
            'message' => 'Email tra cứu đơn hàng đã được gửi. Vui lòng kiểm tra hộp thư của bạn.',
            'email' => $email,
            'order_count' => $orders->count(),
        ]);
    }


    /**
     * Danh sách đơn hàng cho admin
     */
    public function adminIndex(Request $request)
    {
        $query = Order::withTrashed()->with(['user']);

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

        // Filter by status - ALWAYS apply if provided (all statuses)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Fixed: ASC for all statuses including pending (oldest first)
        $orders = $query->orderBy('created_at', 'asc')->paginate($request->per_page ?? 15);

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
'status' => 'required|string|in:pending,processing,shipping,completed,cancelled',
        ]);

        $order = Order::where('order_id', $order_id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        $order->update(['status' => $validated['status']]);

        // Dispatch event if order is completed — listener sends completion email via queue
        if ($validated['status'] === 'completed') {
            OrderCompleted::dispatch($order->fresh(['items.product', 'items.variant']));
        }

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
     * Admin hủy/xóa đơn hàng (soft delete + cancelled status)
     */
    public function adminDestroy(Request $request, $order_id)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string|max:500'
        ]);

        $order = Order::withTrashed()->with('items')->where('order_id', $order_id)->firstOrFail();

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Không thể hủy đơn hàng đã hoàn thành hoặc đã hủy',
            ], 400);
        }

        // Hoàn lại stock nếu có
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                \App\Models\ProductVariant::where('variant_id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            }
        }

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $validated['cancel_reason']
        ]);
        $order->items()->delete(); // soft cascade
        $order->delete(); // soft delete

        return response()->json([
            'message' => 'Đã hủy đơn hàng thành công',
            'success' => true,
        ]);
    }

    /**
     * Tạo mã đơn hàng tự động: FT-YYYYMMDD-XXXX
     */
    private function generateOrderCode()
    {
        $today = now()->format('Ymd');
        $prefix = 'FT-' . $today . '-';

        // Lấy đơn hàng cuối cùng trong ngày với withTrashed
        $lastOrder = Order::withTrashed()->where('order_code', 'like', $prefix . '%')
            ->orderBy('order_code', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastOrder && $lastOrder->order_code) {
            $lastNumber = (int) substr($lastOrder->order_code, strlen($prefix));
        }
        $newNumber = $lastNumber + 1;

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
            'pending' => 33,
            'shipping' => 66,
            'completed' => 100,
        ];
        return $progress[$status] ?? 0;
    }
}
