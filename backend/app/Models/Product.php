<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name', 'slug', 'description', 'short_desc',
        'base_price', 'discount_price', 'stock_total',
        'likes_count', 'category_id', 'is_visible', 'is_featured',
        'thumbnail',
    ];

    protected $casts = [
        'base_price'     => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_visible'     => 'boolean',
        'is_featured'    => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'product_id')->whereNull('parent_id'); // Chỉ lấy comment gốc
    }

    // Accessor ví dụ: giá cuối cùng
    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->base_price;
    }

    /**
     * Real total stock from variants sum (accurate warehouse total)
     */
    public function getRealStockTotalAttribute()
    {
        return $this->variants()->sum('stock');
    }
}

