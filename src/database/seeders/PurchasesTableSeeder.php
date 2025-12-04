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
        // 1件目：取引中の商品（腕時計 15,000円）
        Purchase::create([
            'buyer_id'        => 3,
            'product_id'      => 1,
            'amount'          => 15000,
            'payment_method'  => 'card',
            'status'          => 'trading',
            'payment_intent_id' => null,           // 今回は Stripe 連携しないので null
            'session_id'        => null,           // 同上
            'buyer_rating'     => null,   // ★ 評価なし
            'seller_rating'    => null,   // ★ 評価なし
            'paid_at'          => Carbon::now(),
        ]);
        // 2件目：取引が完了した商品(マイク 8,000円)
        Purchase::create([
            'buyer_id'         => 3,
            'product_id'       => 6,
            'amount'           => 8000,
            'payment_method'   => 'card',
            'status'           => Purchase::STATUS_COMPLETED,  // 取引完了
            'payment_intent_id' => null,
            'session_id'       => null,
            'buyer_rating'    => 4,
            'seller_rating'    => 2,
            'paid_at'          => Carbon::now()->subDay(),
        ]);
    }
}
