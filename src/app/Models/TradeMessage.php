<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /** このメッセージが属する取引 */
    public function trade()
    {
        // 第2引数 'trade_id' は外部キー
        return $this->belongsTo(Purchase::class, 'trade_id');
    }

    /** このメッセージを書いたユーザー */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}