<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'admin_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'full_name',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Mutator để hash password
    public function setPasswordHashAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    // Kiểm tra password
    public function checkPassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    // Scope chỉ lấy admin đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
