<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeMessageRequest;
use App\Models\Purchase;
use App\Models\TradeMessage;
use Carbon\Carbon;

use Illuminate\Http\Request;

class TradeChatController extends Controller
{
    public function show(Purchase $purchase)
{
    // -------------------------------
    // ① 関連モデルをまとめてロード
    // -------------------------------
    // ・product.user ... 商品の出品者
    // ・buyer          ... 購入者
    // ・messages.user  ... メッセージを書いたユーザー
    $purchase->load(['product.user', 'buyer', 'messages.user']);

    // ログインユーザー
    $user = auth()->user();

    // 購入者（purchases.buyer_id）
    $buyer = $purchase->buyer;

    // 出品者（products.user_id のユーザー）
    $seller = $purchase->product->user;

    // 取引相手（チャット画面に表示する相手）
    $partner = null;

    // 自分の立場（buyer / seller / guest）
    $role = 'guest';

    // -------------------------------
    // ② 自分が buyer / seller どちらか判定
    // -------------------------------
    if ($user) {
        // 自分が購入者
        if ($buyer && $buyer->id === $user->id) {
            $partner = $seller;   // 相手は出品者
            $role    = 'buyer';

        // 自分が出品者（= 商品の user）
        } elseif ($seller && $seller->id === $user->id) {
            $partner = $buyer;    // 相手は購入者
            $role    = 'seller';

        // どちらでもない（本来は入ってこない想定）
        } else {
            $partner = $buyer ?? $seller;
            $role    = 'guest';
        }
    }

    // -------------------------------
    // ③ 既読フラグの更新
    // -------------------------------
    if ($user) {
        if ($buyer && $buyer->id === $user->id) {
            // 自分が購入者なら buyer_last_read_at を更新
            $purchase->buyer_last_read_at = now();
        } elseif ($seller && $seller->id === $user->id) {
            // 自分が出品者なら seller_last_read_at を更新
            $purchase->seller_last_read_at = now();
        }

        $purchase->save();
    }

    // -------------------------------
    // ④ その他の取引一覧（左カラム）
    // -------------------------------
    $otherTrades = $user
        ? $user->purchases()                  // ログインユーザーの購入履歴
            ->where('id', '!=', $purchase->id) // 今見ている取引以外
            ->with('product')
            ->latest('created_at')
            ->get()
        : collect();                           // 未ログインなら空コレクション

    // -------------------------------
    // ⑤ メッセージ一覧
    // -------------------------------
    $messages = $purchase->messages()
        ->with('user')
        ->orderBy('created_at')
        ->get();

    // 商品情報（ヘッダー表示用）
    $product = $purchase->product;

    // -------------------------------
    // ⑥ 画面に渡す
    // -------------------------------
    return view('trades.chat', compact(
        'purchase',
        'product',
        'messages',
        'otherTrades',
        'partner', // 取引相手（ヘッダーに表示）
        'role'     // 自分の立場（購入者 / 出品者 / ゲスト）
    ));
}

    /** メッセージ送信処理 */
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
