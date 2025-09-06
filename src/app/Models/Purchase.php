<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_id','amount','payment_method','status','paid_at','payment_intent_id'];

    public const STATUS_TRADING    = 'trading';
    public const STATUS_COMPLETED  = 'completed';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'buyer_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }


}
