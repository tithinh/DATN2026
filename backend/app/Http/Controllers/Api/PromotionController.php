<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function active()
    {
        return Promotion::where('is_active', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->whereNull('max_uses')
                  ->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->get();
    }

    public function available(Request $request)
    {
        $subtotal = $request->input('subtotal', 0);

        $query = Promotion::where('is_active', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        // Exclude exhausted promos
        $query->where(function ($q) {
            $q->whereNull('max_uses')
              ->orWhereColumn('used_count', '<', 'max_uses');
        });

        // Exclude promos already used by authenticated user
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user();
        if ($user) {
            $usedPromoIds = \App\Models\Order::where('user_id', $user->user_id)
                ->whereNotNull('promo_id')
                ->pluck('promo_id');
            $query->whereNotIn('promo_id', $usedPromoIds);
        }

        $promotions = $query->get();

        // Attach calculated discount for each promo based on subtotal
        $promotions = $promotions->map(function ($promo) use ($subtotal) {
            $discountAmount = 0;
            $isValid = true;
            $reason = null;

            if ($subtotal > 0 && $promo->min_order_amount > 0 && $subtotal < $promo->min_order_amount) {
                $isValid = false;
                $reason = 'Đơn hàng tối thiểu ' . number_format($promo->min_order_amount) . '₫';
            }

            if ($isValid && $subtotal > 0) {
                if ($promo->promo_type === 'percentage') {
                    $discountAmount = $subtotal * ($promo->discount_value / 100);
                } elseif ($promo->promo_type === 'fixed') {
                    $discountAmount = $promo->discount_value;
                }
                $discountAmount = min($discountAmount, $subtotal);
            }

            return [
                'promo_id' => $promo->promo_id,
                'code' => $promo->code,
                'promo_type' => $promo->promo_type,
                'discount_value' => $promo->discount_value,
                'discount_amount' => round($discountAmount),
                'min_order_amount' => $promo->min_order_amount,
                'is_valid' => $isValid,
                'reason' => $reason,
            ];
        });

        return response()->json([
            'data' => $promotions->values(),
        ]);
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        $promo = Promotion::where('code', $request->code)
            ->where('is_active', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json(['error' => 'Mã không hợp lệ hoặc đã hết hạn'], 422);
        }

        // Kiểm tra số lần sử dụng
        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return response()->json(['error' => 'Mã giảm giá đã hết lượt sử dụng'], 422);
        }

        // Kiểm tra user đã sử dụng chưa
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user();
        if ($user) {
            $alreadyUsed = \App\Models\Order::where('user_id', $user->user_id)
                ->where('promo_id', $promo->promo_id)
                ->exists();
            if ($alreadyUsed) {
                return response()->json(['error' => 'Bạn đã sử dụng mã giảm giá này trước đó'], 422);
            }
        }

        $subtotal = $request->input('subtotal', 0);

        // Kiểm tra đơn tối thiểu
        if ($promo->min_order_amount > 0 && $subtotal < $promo->min_order_amount) {
            return response()->json([
                'error' => 'Đơn hàng tối thiểu ' . number_format($promo->min_order_amount) . '₫ để áp dụng mã này',
            ], 422);
        }

        // Tính số tiền giảm
        $discountAmount = 0;
        if ($promo->promo_type === 'percentage') {
            $discountAmount = $subtotal * ($promo->discount_value / 100);
        } elseif ($promo->promo_type === 'fixed') {
            $discountAmount = $promo->discount_value;
        }

        // Đảm bảo không giảm quá subtotal
        $discountAmount = min($discountAmount, $subtotal);

        return response()->json([
            'promo_id' => $promo->promo_id,
            'code' => $promo->code,
            'promo_type' => $promo->promo_type,
            'discount_value' => $promo->discount_value,
            'discount_amount' => round($discountAmount),
            'min_order_amount' => $promo->min_order_amount,
            'message' => 'Áp dụng mã giảm giá thành công!',
        ]);
    }

    // =====================
    // ADMIN METHODS
    // =====================

    public function adminIndex(Request $request)
    {
        $query = Promotion::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->input('per_page', 10);
        $promotions = $query->paginate($perPage);

        return response()->json([
            'data' => $promotions->items(),
            'total' => $promotions->total(),
            'current_page' => $promotions->currentPage(),
            'last_page' => $promotions->lastPage(),
            'per_page' => $promotions->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:promotions,code',
            'promo_type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $data['used_count'] = 0;
        $promotion = Promotion::create($data);

        return response()->json(['message' => 'Tạo voucher thành công', 'data' => $promotion], 201);
    }

    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return response()->json($promotion);
    }

    public function update($id, Request $request)
    {
        $promotion = Promotion::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|unique:promotions,code,' . $promotion->promo_id . ',promo_id',
            'promo_type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $promotion->update($data);

        return response()->json(['message' => 'Cập nhật voucher thành công', 'data' => $promotion]);
    }

    public function toggleStatus($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => !$promotion->is_active]);

        return response()->json([
            'message' => $promotion->is_active ? 'Đã kích hoạt voucher' : 'Đã vô hiệu hóa voucher',
            'is_active' => $promotion->is_active,
        ]);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(['message' => 'Đã xóa voucher thành công']);
    }
}
