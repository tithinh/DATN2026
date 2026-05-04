<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'variant_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'color',
        'storage_size',
        'name',
        'price_extra',    // giá cộng thêm (legacy)
        'sku',
        'stock',
        'image_urls',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'image_urls' => 'array',
        'price_extra' => 'float',
        'price' => 'float',
        'stock' => 'integer',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the order items associated with the variant.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id', 'variant_id');
    }

    /**
     * Get the cart items associated with the variant (nếu bạn có bảng cart_items).
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'variant_id', 'variant_id');
    }

    /**
     * Accessor: Giá cuối cùng của variant
     * Ưu tiên: price (trực tiếp) → base_price + price_extra (legacy)
     */
    public function getFinalPriceAttribute(): float
    {
        if ($this->price !== null) {
            return (float) $this->price;
        }
        $basePrice = $this->product->discount_price ?? $this->product->base_price ?? 0;
        return (float) $basePrice + ($this->price_extra ?? 0);
    }

    /**
     * Scope: Variants còn hàng (stock > 0)
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Variants theo màu
     */
    public function scopeByColor($query, string $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Auto-update parent product stock_total when this variant stock changes
     */
    protected static function booted()
    {
        static::saved(function (self $variant) {
            $product = $variant->product;
            if ($product) {
                $product->stock_total = $product->real_stock_total;
                $product->saveQuietly();
            }
        });

        static::deleting(function (self $variant) {
            $product = $variant->product;
            if ($product) {
                $product->stock_total = $product->real_stock_total;
                $product->saveQuietly();
            }
        });
    }
}
