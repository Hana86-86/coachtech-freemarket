<?php

namespace App\Models;

use App\Models\TradeMessage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'product_id',
        'amount',
        'payment_method',
        'status',
        'payment_intent_id',
        'session_id',
        'paid_at',
        'buyer_rating',
        'seller_rating',
    ];
    protected $casts = [
        'paid_at'       => 'datetime',
        'buyer_rating'  => 'integer',
        'seller_rating' => 'integer',
    ];

    public const STATUS_AWAITING   = 'awaiting_payment';
    public const STATUS_PAID       = 'paid';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_EXPIRED    = 'expired';
    public const STATUS_TRADING    = 'trading';
    public const STATUS_COMPLETED  = 'completed';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function messages()
    {
        return $this->hasMany(TradeMessage::class, 'trade_id');
    }
    public function scopeTradingForBuyer($query, int $userId)
    {
        // buyer_idが指定ユーザーかつstatusがtradingのものだけに絞る
        return $query
            ->where('buyer_id', $userId)
            ->where('status', self::STATUS_TRADING);
    }
}
