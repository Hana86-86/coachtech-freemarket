<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeMessageRequest;
use App\Mail\TradeCompletedMail;
use App\Models\Purchase;
use App\Models\TradeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class TradeChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $purchase->load(['product.user', 'buyer', 'messages.user']);

        $user = auth()->user();

        $buyer = $purchase->buyer;

        $seller = $purchase->product->user;

        $partner = null;

        $role = 'guest';

        if ($user) {
            if ($buyer && $buyer->id === $user->id) {
                $partner = $seller;
                $role    = 'buyer';
            } elseif ($seller && $seller->id === $user->id) {
                $partner = $buyer;
                $role    = 'seller';
            } else {
                $partner = $buyer ?? $seller;
                $role    = 'guest';
            }
        }

        if ($user) {
            if ($buyer && $buyer->id === $user->id) {
                $purchase->buyer_last_read_at = now();
            } elseif ($seller && $seller->id === $user->id) {
                $purchase->seller_last_read_at = now();
            }

            $purchase->save();
        }

        $otherTrades = $user
            ? $user->purchases()
            ->where('id', '!=', $purchase->id)
            ->with('product')
            ->latest('created_at')
            ->get()
            : collect();

        $messages = $purchase->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $product = $purchase->product;

        return view('trades.chat', compact(
            'purchase',
            'product',
            'messages',
            'otherTrades',
            'partner',
            'role'
        ));
    }

    // メッセージ送信処理
    public function store(StoreTradeMessageRequest $request, Purchase $purchase)
    {
        $validated = $request->validated();

        $message = new TradeMessage();
        $message->trade_id = $purchase->id;
        $message->user_id = auth()->id();
        $message->body     = $validated['body'] ?? null;
        $message->is_system = false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('trade_messages',  'public');
            $message->image_path = $path;
        }

        $message->save();

        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', 'メッセージを送信しました。');
    }
    private function authorizeMessageOwner(TradeMessage $message): void
    {
        if (auth()->id() !== $message->user_id) {
            abort(403, 'このメッセージを編集・削除する権限がありません。');
        }
    }
    // チャットメッセージを編集（更新）する
    public function update(Request $request, TradeMessage $message)
    {
        $this->authorizeMessageOwner($message);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);
        $message->update([
            'body' => $validated['body'],
        ]);

        $purchase = $message->purchase;

        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', 'メッセージを更新しました。');
    }
    // チャットメッセージ削除
    public function destroy(TradeMessage $message)
    {
        $this->authorizeMessageOwner($message);
        $purchase = $message->purchase;
        $message->delete();

        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', 'メッセージを削除しました。');
    }
    // 評価の保存
    public function rate(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ], [
            'rating.required' => '評価を選択してください。',
            'rating.integer'  => '評価は数値で指定してください。',
            'rating.min'      => '1〜5の範囲で評価してください。',
            'rating.max'      => '1〜5の範囲で評価してください。',
        ]);

        $user = $request->user();
        $isBuyer  = ($purchase->buyer_id === $user->id);
        $isSeller = ($purchase->product->user_id === $user->id);

        if (! $isBuyer && ! $isSeller) {
            abort(403, 'この取引の当事者のみ評価できます。');
        }
        if ($isBuyer && !is_null($purchase->buyer_rating)) {
            return back()->with('success', '既に購入者として評価済みです。');
        }
        if ($isSeller && !is_null($purchase->seller_rating)) {
            return back()->with('success', '既に出品者として評価済みです。');
        }

        if ($isBuyer) {
        $purchase->buyer_rating = $validated['rating'];
    }

    if ($isSeller) {
        $purchase->seller_rating = $validated['rating'];
    }


        $purchase->save();

    if ($isBuyer) {
        $seller = $purchase->product->user;

        if ($seller && $seller->email) {
            Mail::to($seller->email)
                ->send(new TradeCompletedMail($purchase));
        }
    }

        return redirect()
            ->route('products.index')
            ->with('success', '評価を送信しました。(取引完了メールを送信しました)');
    }
    public function complete(Request $request, Purchase $purchase)
    {
        $user = $request->user();

        if ($purchase->buyer_id !== $user->id) {
            abort(403, '取引を完了できるのは購入者のみです。');
        }

        if ($purchase->status === 'completed') {
            return back()->with('success', 'すでに取引は完了しています。');
        }

        $purchase->status = 'completed';
        $purchase->save();

        return back()->with('success', '取引を完了しました。評価を入力してください。');
    }
}
