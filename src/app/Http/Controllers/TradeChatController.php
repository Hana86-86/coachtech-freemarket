<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\TradeMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TradeChatController extends Controller
{
    /** チャット画面の表示 */
    public function show(Purchase $purchase)
    {
        $user = auth()->user();

        // ★ 自分が買い手 or 売り手かで、更新するカラムを切り替える
        if ($user->id === $purchase->buyer_id) {
            $purchase->buyer_last_read_at = now();   // 今読んだ
        } else {
            $purchase->seller_last_read_at = now();
        }
        $purchase->save();

        // 左カラム「その他の取引」
        $otherTrades = $user->purchases()
            ->where('id', '!=', $purchase->id)
            ->with('product')
            ->latest('created_at')
            ->get();

        // メッセージ一覧
        $messages = $purchase->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $product = $purchase->product;

        return view('trades.chat', compact('purchase', 'product', 'messages', 'otherTrades'));
    }

    /** メッセージ送信処理 */
    public function store(Request $request, Purchase $purchase)
    {

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        TradeMessage::create([
            'trade_id' => $purchase->id,
            'user_id'  => $request->user()->id,
            'body'     => $validated['body'],
        ]);

        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', 'メッセージを送信しました。');
    }
    /** 評価保存 */
    public function storeRating(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ], [
            'rating.required' => '評価を選択してください。',
            'rating.integer'  => '評価は数値で指定してください。',
            'rating.min'      => '1〜5の範囲で評価してください。',
            'rating.max'      => '1〜5の範囲で評価してください。',
        ]);
        if ($request->user()->id !== $purchase->buyer_id) {
            abort(403, 'この取引を評価できるのは購入者だけです。');
        }
        $purchase->buyer_rating = $validated['rating'];
        $purchase->save();

        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', '評価を送信しました。');
    }
    /** メッセージの所有者かどうかをチェックする共通メソッド */
    private function authorizeMessageOwner(TradeMessage $message): void
    {
        if (auth()->id() !== $message->user_id) {
            abort(403, 'このメッセージを編集・削除する権限がありません。');
        }
    }
    /** チャットメッセージを編集（更新）する */
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
    /** チャットメッセージを削除する */
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
        // 1. 入力チェック（1〜5 の整数）
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        // 2. 今ログインしているユーザー
        $user = $request->user();

        // 3. 購入者かどうか判定
        $isBuyer  = ($purchase->buyer_id === $user->id);
        // 4. 出品者かどうか判定（商品を出品したユーザー）
        $isSeller = ($purchase->product->user_id === $user->id);

        // 5. どちらにも該当しなければ 403
        if (! $isBuyer && ! $isSeller) {
            abort(403, 'この取引の当事者のみ評価できます。');
        }

        // 6. 既に評価済みなら更新しない（お好みで）
        if ($isBuyer && !is_null($purchase->buyer_rating)) {
            return back()->with('success', '既に購入者として評価済みです。');
        }
        if ($isSeller && !is_null($purchase->seller_rating)) {
            return back()->with('success', '既に出品者として評価済みです。');
        }

        // 7. 役割に応じて保存先のカラムを切り替え
        if ($isBuyer) {
            $purchase->buyer_rating = $validated['rating'];
        }
        if ($isSeller) {
            $purchase->seller_rating = $validated['rating'];
        }

        $purchase->save();

        return back()->with('success', '評価を送信しました。');
    }
}
