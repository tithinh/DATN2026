<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id'; // ← Quan trọng! Khớp với bảng

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'is_featured',
        'color',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Quan hệ danh mục cha
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'category_id');
    }

    // Quan hệ danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'category_id');
    }

    // Quan hệ sản phẩm thuộc danh mục này
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    // Scope chỉ lấy danh mục đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
