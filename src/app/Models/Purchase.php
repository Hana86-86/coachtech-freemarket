<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_id','amount','payment_method','status','paid_at'];

    public function product(){ return $this->belongsTo(Product::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
