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
        $sigHeader     = $request->header('Stripe-Signature');
        $secret  = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        }   catch(\UnexpectedValueException|\Stripe\Exception\SignatureVerificationException $e) {
            return response('invalid', 400);
        }
            switch ($event->type) {
                case 'payment_intent.succeeded';
                {
                    $pi = $event->data->object;
                    $piId      = $pi->id;
                    $productId = (int)($pi->metadata->product_id ?? 0);
                    $buyerId   = (int)($pi->metadata->buyer_id ?? 0);
                    $method    = $pi->payment_method_type[0] ?? 'unknown';
                    $amount    = (int)($pi->amount ?? 0)/100;

                    DB::transaction(function () use ($piId, $productId, $buyerId, $method,$amount) {
                    // 取引レコード更新・作成
                    Purchase::updateOrCreate(
                        ['payment_intent_id' => $piId],
                        [
                            'buyer_id'       => $buyerId,
                            'product_id'     => $product->id,
                            'amount'         => $product->price,
                            'payment_method' => $method,
                            'status'         => Purchase::STATUS_COMPLETED,
                            'payd_at'        => now(),
                        ]
                        );
                        $product = Product::find($productId);
                        if ($product && $product->sale_status !== Product::SALE_STATUS_SOLD) {
                            $product->update(['sale_status' => Product::SALE_STATUS_SOLD]);
                        }
                });
            return response('ok', 200);
        }
    }

        return response('ok', 200);

}
}
