<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'author',
        'views',
        'status'
    ];

    protected $casts = [
        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Generate slug from title
    public static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = \Str::slug($news->title);
            }
        });
    }
}
