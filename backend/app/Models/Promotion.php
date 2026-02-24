<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $primaryKey = 'promo_id';

    protected $fillable = [
        'code', 'promo_type', 'discount_value', 'min_order_amount',
        'max_uses', 'used_count', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'discount_value'   => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active'        => 'boolean',
        'start_date'       => 'date',
        'end_date'         => 'date',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'promo_id');
    }
}
