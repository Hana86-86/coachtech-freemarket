<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'brand',
        'description',
        'price',
        'category_id',
        'image_path',
        'condition',
        'sale_status',
    ];

    public const SALE_STATUS_PUBLIC = '公開中';
    public const SALE_STATUS_TRADING = '取引中';
    public const SALE_STATUS_SOLD = '売却済';

    public function getIsSoldAttribute():bool
    {
        return $this->sale_status === self::SALE_STATUS_SOLD;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
        product::withCount('favorites')->get();
    }
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)
        ->latest();
    }
    
}
