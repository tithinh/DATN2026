<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * Relationship: Sản phẩm chính mà item này thuộc về
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship: Biến thể sản phẩm (nếu có)
     * Lưu ý: Nếu variant_id là nullable, relationship này có thể null
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    /**
     * Relationship: Người dùng sở hữu item này (nếu đăng nhập)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor: Giá cuối cùng của item này (dựa trên variant hoặc sản phẩm gốc)
     *
     * @return float
     */
    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->variant?->final_price ?? $this->product?->price ?? 0;
        return $basePrice * $this->quantity;
    }

    /**
     * Accessor: Tên hiển thị của item (ưu tiên variant name nếu có)
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->variant) {
            return $this->product->name . ' - ' . $this->variant->name;
        }
        return $this->product->name ?? 'Sản phẩm không xác định';
    }

    /**
     * Scope: Chỉ lấy các item của user hiện tại (đã đăng nhập)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Chỉ lấy các item theo session (cho khách chưa đăng nhập)
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Các item có variant cụ thể
     */
    public function scopeByVariant($query, $variantId)
    {
        return $query->where('variant_id', $variantId);
    }

    /**
     * Kiểm tra xem item có variant không
     */
    public function hasVariant(): bool
    {
        return $this->variant_id !== null;
    }

}
