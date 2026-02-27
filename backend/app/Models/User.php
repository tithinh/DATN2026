<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Comment;
use App\Models\Cart;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'address',
        'role',
        'is_active',
        'avatar',
        'birthday',
        'gender',
        'google_id',
        'facebook_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Mutator hash password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Scope admin
    public function scopeAdmin($query)
    {
        return $query->where('role', 'like', '%admin%');
    }

    // Kiểm tra quyền admin
    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }
 /**
     * Kiểm tra user có active không
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }

}
