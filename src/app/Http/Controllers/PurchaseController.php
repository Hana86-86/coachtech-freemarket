<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function confirm(Request $request,$id)
    {
        $product = Product::findOrFail($id);
        //デフォルトは Card
        $method = $request->query('method','card');

        return view('purchase.confirm', compact('product','method'));
    }

    public function checkout(PurchaseRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $method = $request->input('payment_method', 'card');
        // Stripe初期化
        Stripe::setApiKey(env('STRIPE_SECRET'));
        //Checkoutセッション
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' =>'jpy',
                    'product_data' => [
                        'name' => $product->title,
                    ],
                    'unit_amount' => $product->price, // 金額（円->最小単位）
                ],
                'quantity' =>1,
            ]],
            'mode' =>'payment',
            'success_url' => route('purchase.success').'?pid='.$product->id.'&method='.$method,
            'cancel_url' => route('purchase.confirm', ['id'=>$product->id,'method'=>$method]),
            ]);
            return redirect($session->url);
            }
        public function success(Request $request)
    {
        $productId = (int) $request->query('pid');
        $method    = $request->query('method','card');

        $product = Product::findOrFail($productId);

        DB::transaction(function () use ($product, $method) {
            // 在庫ガード
            if ($product->sale_status === Product::SALE_STATUS_SOLD) {
                abort(409, 'この商品は売り切れです');
            }
            // 売却済に更新
            $product->update(['sale_status' => Product::SALE_STATUS_SOLD]);

            // 購入履歴を保存
            Purchase::create([
                'user_id'        => Auth::id(),
                'product_id'     => $product->id,
                'amount'         => $product->price,
                'payment_method' => $method,      // 'card' or 'konbini'(UI選択値)
                'status'         => 'paid',
                'paid_at'        => now(),
            ]);
        });
        return view('purchase.success');
    }

    }

