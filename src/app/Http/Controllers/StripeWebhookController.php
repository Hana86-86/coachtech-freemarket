<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook as StripeWebhook;
use Stripe\Stripe;
use App\Http\Product;
use App\Http\Purchase;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');
        $secret  = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = StripeWebhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature error: '.$e->getMessage());
            return response('bad signature', 400);
        }
        // コンビニは「実際の支払い完了」でpayment_intent_succeededが飛ぶ
        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
        // checkout作成時の metadata　を使って特定
            $productId = (int)($pi->metadata->product_id ?? 0);
            $buyerId   = (int)($pi->metadata->buyer_id ?? 0);
            $method    = $pi->payment_method_types[0] ?? 'unknown';

            if ($productId && $buyerId) {
                $product = Product::find($productId);

                if ($product) {
                    DB::transaction(function () use ($pi, $product, $buyerId, $method) {
                    // 購入確定
                     Purchase::updateOrCreate(
                        ['payment_intent_id' => $pi->id],
                        [
                            'buyer_id'       => $buyerId,
                            'product_id'     => $product->id,
                            'amount'         => $product->price,
                            'payment_method' => $method,
                            'status'         => Purchase::STATUS_COMPLETED,
                            'payd_at'        => now(),
                        ]
                        );
                        if ($product->sale_status !== Product::SALE_STATUS_SOLD) {
                            $product->update(['sale_status' => Product::SALE_STATUS_SOLD]);
                        }
                });
            }
        }
    }

        return response('ok', 200);
    }
}
