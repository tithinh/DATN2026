<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    // Khai báo primary key là comment_id (thay vì id mặc định)
    protected $primaryKey = 'comment_id';

    // Nếu comment_id là auto-increment integer (thường là vậy)
    public $incrementing = true;

    // Nếu comment_id không phải integer (ít gặp), thêm dòng này
    // protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'content',
        'rating',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Quan hệ với sản phẩm
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Quan hệ với người dùng bình luận
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Bình luận cha (nếu là reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Các bình luận trả lời (replies)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope: Chỉ bình luận thuần (không có sao đánh giá)
     */
    public function scopeComments($query)
    {
        return $query->whereNull('rating')->orWhere('rating', 0);
    }

    /**
     * Scope: Chỉ đánh giá có sao (1-5 sao)
     */
    public function scopeRatings($query)
    {
        return $query->where('rating', '>', 0);
    }

    /**
     * Accessor: Xác định loại - 'comment' hoặc 'rating'
     */
    public function getTypeAttribute(): string
    {
        return $this->rating > 0 ? 'rating' : 'comment';
    }

    /**
     * Scope cho admin: Lọc theo loại
     */
    public function scopeByType($query, $type)
    {
        if ($type === 'comment') {
            return $query->comments();
        } elseif ($type === 'rating') {
            return $query->ratings();
        }
        return $query;
    }

    /**
     * Scope: Check if user already rated this product (rated > 0)
     */
    public function scopeUserRated($query, $userId, $productId)
    {
        return $query->where('user_id', $userId)
                     ->where('product_id', $productId)
                     ->ratings(); // rating > 0
    }
}
