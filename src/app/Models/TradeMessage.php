<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TradeMessage extends Model
{
    use HasFactory;

    // 一括代入を許可するカラム
    protected $fillable = [
        'trade_id',
        'user_id',
        'body',
        'is_system',
        'type',
        'image_path',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        return Storage::url($this->image_path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function purchase()
{
    return $this->belongsTo(Purchase::class, 'trade_id');
}
}