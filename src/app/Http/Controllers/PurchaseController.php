<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use Stripe\PaymentIntent as StripePaymentIntent;

class PurchaseController extends Controller
{
    public function confirm(Request $request,$id)
    {
        $product = Product::findOrFail($id);
        //デフォルトは Card
        $paymentMethod = $request->query('payment_method','card');

        if (!$request->boolean('resume')) {
            session()->forget('checkout_shipping');
        }

        return view('purchase.confirm', compact('product','paymentMethod'));
    }

    public function checkout(PurchaseRequest $request, $id)
{
    $product = Product::findOrFail($id);
    $method  = $request->input('payment_method', 'card');

    if ($product->sale_status === Product::SALE_STATUS_SOLD) {
        return back()->with('error', '売却済みの商品です。');
    }

    Stripe::setApiKey(env('STRIPE_SECRET'));

    $params = [
        'mode'                  => 'payment',
        'payment_method_types'  => [$method === 'konbini' ? 'konbini' : 'card'],
        'locale'                => 'ja',
        'line_items' => [[
            'price_data' => [
                'currency'    => 'jpy',
                'unit_amount' => (int) $product->price,   // int 必須
                'product_data' => [
                    'name' => (string) $product->title,
                ],
            ],
            'quantity' => 1,
        ]],
        // 成功時に商品特定できるようにするため metadata を PaymentIntent に付与
        'payment_intent_data' => [
            'metadata' => [
                'product_id' => (string) $product->id,
                'seller_id'  => (string) $product->user_id,
                'buyer_id'   => (string) auth()->id(),
            ],
        ],
        'success_url' => route('purchase.success', ['id' => $product->id]) . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => route('purchase.confirm', ['id' => $product->id]),
    ];
        // コンビニ払いのオプション
        if ($method === 'konbini') {
            $params['payment_method_options'] = [
                'konbini' => ['expires_after_days' => 3],
            ];
        }

        $session = StripeSession::create($params);

        // コンビニはここで予約（SOLD）& 購入レコードPENDINGを作る
        if ($method === 'konbini') {
            \DB::transaction(function () use ($product, $session) {
                if ($product->sale_status !== Product::SALE_STATUS_SOLD) {
                    $product->update(['sale_status' => Product::SALE_STATUS_SOLD]);
                }
                Purchase::firstOrCreate(
                    ['session_id' => $session->id],
                    [
                        'session_id'  => $session->id,
                        'buyer_id'      => auth()->id(),
                        'product_id'   => $product->id,
                        'amount'       => $product->price,
                        'payment_method' => 'konbini',
                        'status'         => Purchase::STATUS_AWAITING,
                        'payment_intent_id' => $session->payment_intent ?? null,
                    ]
                    );
            });
        }

        return redirect($session->url);
    }

        public function success(Request $request, $id)
    {

        $product = Product::findOrFail($id);
        // StripeのセッションIDをクエリから取得
        $csid   = $request->query('session_id');
        $method = 'unknown';

        if ($csid) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
        // 支払い方法と PaymentIntent を取得
            $session = StripeSession::retrieve($csid, ['expand' => ['payment_intent']]);

        // 決済手段
            $method = $session->payment_method_types[0] ?? 'unknown';

            $pi = $session->payment_intent;

            if (is_string($pi)) {
            $pi = StripePaymentIntent::retrieve($pi);
        }

        if ($method === 'card' && $pi && $pi->status === 'succeeded') {
            DB::transaction(function () use ($pi, $product, $method) {
                // 重複作成防止：同じ payment_intent_id があれば作らない
                Purchase::firstOrCreate(
                    ['payment_intent_id' => $pi->id],
                    [
                        'buyer_id'       => auth()->id(),
                        'product_id'     => $product->id,
                        'amount'         => $product->price,
                        'payment_method' => $method,
                        'status'         => Purchase::STATUS_COMPLETED,
                        'paid_at'        => now(),
                    ]
                );

                // 既に SOLD でなければ更新
                if ($product->sale_status !== Product::SALE_STATUS_SOLD) {
                    $product->update(['sale_status' => Product::SALE_STATUS_SOLD]);
                }
            });
        }

    }
        session()->forget('checkout_shipping');

        return redirect()->route('products.index')->with('success', '購入が完了しました。');
    }

}