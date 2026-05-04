<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'order_id';
    protected $fillable = [
        'user_id',
        'order_code',
        'promo_id',
        'discount_amount',
        'final_amount',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address',
        'note',
        'shipping_method',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'payment_status',
    ];


    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'status' => 'string',
        'payment_method' => 'string',
        'payment_status' => 'string',
        'deleted_at' => 'datetime',
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Thêm product thông qua items
    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'product_id');
    }
}
