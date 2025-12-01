<?php

namespace App\Http\Controllers;

use App\Models\Purchase; 
use App\Models\TradeMessage;
use Illuminate\Http\Request;

class TradeChatController extends Controller
{
    /** チャット画面の表示 */
    public function show(Purchase $purchase)
    {
        // この取引のメッセージを 古い順 に取得（ユーザーも一緒に）
        $messages = $purchase->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('trades.chat', [
            'purchase'    => $purchase,
            'messages' => $messages,
        ]);
    }

    /** メッセージ送信処理 */
    public function store(Request $request, Purchase $purchase)
    {
        // バリデーション
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        // メッセージ保存
        TradeMessage::create([
            'trade_id' => $purchase->id,
            'user_id'  => $request->user()->id,
            'body'     => $validated['body'],
        ]);

        // 元の画面に戻る
        return redirect()
            ->route('trades.chat.show', $purchase)
            ->with('success', 'メッセージを送信しました。');
    }
    
}