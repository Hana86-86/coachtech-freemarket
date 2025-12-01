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
        // 1件目：取引中の注文（腕時計 15,000円）
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
        // 2件目：取引が完了した注文（同じ腕時計を別の取引とみなすダミー）
        Purchase::create([
            'buyer_id'         => 3,                           // 同じ購入者
            'product_id'       => 1,                           // 腕時計
            'amount'           => 15000,
            'payment_method'   => 'card',
            'status'           => Purchase::STATUS_COMPLETED,  // 取引完了
            'payment_intent_id' => null,
            'session_id'       => null,
            'paid_at'          => Carbon::now()->subDay(),
        ]);
    }
}
