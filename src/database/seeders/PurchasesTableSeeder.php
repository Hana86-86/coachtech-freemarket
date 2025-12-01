<?php

namespace Database\Seeders;

use App\Models\Purchase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchasesTableSeeder extends Seeder
{
    /**
     * シーダーのメイン処理
     */
    public function run(): void
    {

        Purchase::create([
            'buyer_id'        => 3,                 // 購入者ユーザーID（3人目のユーザー）
            'product_id'      => 1,
            'amount'          => 15000,
            'payment_method'  => 'card',
            'status'          => 'trading',        // 取引中ステータス
            'payment_intent_id' => null,           // 今回は Stripe 連携しないので null
            'session_id'        => null,           // 同上
            'paid_at'           => Carbon::now(),
        ]);
    }
}
