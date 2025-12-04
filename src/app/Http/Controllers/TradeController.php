<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    /**
     * 取引中の一覧画面
     */
    public function index()
    {
        $userId = Auth::id();

        $trades = Purchase::with(['product.user', 'buyer'])
            ->where('buyer_id', $userId)
            ->orWhereHas('product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', '!=', 'completed')
            ->orderByDesc('created_at')
            ->get();

        return view('trades.index', [
            'trades' => $trades,
            'userId' => $userId,
        ]);
    }

    /**
     * 取引チャット画面
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['product.user', 'buyer']);

        $userId = Auth::id();

        if (
            $purchase->buyer_id !== $userId &&
            $purchase->product->user_id !== $userId
        ) {
            abort(403);
        }

        return view('trades.show', [
            'purchase' => $purchase,
            'userId'   => $userId,
        ]);
    }
    public function chat(Purchase $purchase)
    {
        $purchase->load(['product', 'buyer', 'product.user']);

        $userId = Auth::id();

        if ($purchase->buyer_id !== $userId && $purchase->product->user_id !== $userId) {
            abort(403);
        }
        return view('trades.chat', [
            'purchase' => $purchase,
            'userId'   => $userId,
        ]);
    }
}
