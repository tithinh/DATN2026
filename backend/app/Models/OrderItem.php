<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'order_item_id';

    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'quantity', 'price_at_purchase',
        'rating', 'comment',
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'quantity'          => 'integer',
        'rating'            => 'integer',
        'deleted_at' => 'datetime',
    ];

    // Accessor để dùng price thay cho price_at_purchase
    public function getPriceAttribute()
    {
        return $this->price_at_purchase;
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
