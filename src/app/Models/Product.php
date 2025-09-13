<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'brand',
        'description',
        'price',
        'image_path',
        'condition',
        'sale_status',
    ];

    public const SALE_STATUS_PUBLIC = '公開中';
    public const SALE_STATUS_SOLD = '売却済み';


    public static function getSaleStatusLabel($status)
    {
        return match ($status) {
            self::SALE_STATUS_PUBLIC => '公開中',
            self::SALE_STATUS_SOLD   => '売却済み',
            default => $status,
        };
    }

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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product')
        ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product')
        ->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
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
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/noimage.png');
    }
    // 既存データ（storage/付き）にも対応
        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
    }
        return asset('storage/'.$this->image_path);
}
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('sale_status', [self::SALE_STATUS_PUBLIC, self::SALE_STATUS_SOLD]);
    }
}

