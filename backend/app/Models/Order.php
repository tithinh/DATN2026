<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'user_id', 'promo_id', 'total_amount', 'discount_amount',
        'final_amount', 'status', 'payment_method', 'shipping_address', 'note',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'final_amount'     => 'decimal:2',
        'status'           => 'string',
        'payment_method'   => 'string',
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
}
