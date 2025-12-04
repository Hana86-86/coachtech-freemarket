<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $currentTab = $request->query('tab', 'selling');

        $myProducts = $user->products()
            ->latest('created_at')
            ->get();

        // 自分が購入者側の取引
        $tradingAsBuyer = Purchase::query()
            ->where('buyer_id', $user->id)
            ->where(function ($q) {
                $q->where('status', Purchase::STATUS_TRADING)
                    ->orWhere(function ($q2) {
                        $q2->where('status', Purchase::STATUS_COMPLETED)
                            ->whereNull('buyer_rating'); // 購入者評価がまだ
                    });
            })
            ->with('product')
            ->get();

        // 自分が出品者側の取引
        $tradingAsSeller = Purchase::query()
            ->whereHas('product', function ($sq) use ($user) {
                $sq->where('user_id', $user->id); // 自分が出品した商品
            })
            ->where(function ($q) {
                $q->where('status', Purchase::STATUS_TRADING)
                    ->orWhere(function ($q2) {
                        $q2->where('status', Purchase::STATUS_COMPLETED)
                            ->whereNull('seller_rating'); // 出品者評価がまだ
                    });
            })
            ->with('product')
            ->get();

        $tradingPurchases = $tradingAsBuyer
            ->merge($tradingAsSeller)
            ->sortByDesc('paid_at')
            ->values();

        foreach ($tradingPurchases as $purchase) {

            $isBuyer = ($purchase->buyer_id === $user->id);

            $otherUserId = $isBuyer
                ? $purchase->product->user_id
                : $purchase->buyer_id;

            $lastReadAt = $isBuyer
                ? $purchase->buyer_last_read_at
                : $purchase->seller_last_read_at;

            // trade_messages テーブルから未読をカウント
            $unreadQuery = \App\Models\TradeMessage::query()
                ->where('trade_id', $purchase->id)
                ->where('user_id', $otherUserId);

            // 最後に読んだ時間があれば「それ以降」を未読とみなす
            if ($lastReadAt) {
                $unreadQuery->where('created_at', '>', $lastReadAt);
            }

            $purchase->unread_count = $unreadQuery->count();
        }

        $tradingUnreadTotal = $tradingPurchases->sum('unread_count');

        $completedPurchases = $user->purchases()
            ->where('status', Purchase::STATUS_COMPLETED)
            ->with('product')
            ->latest('paid_at')
            ->get();

        // 平均評価（購入側）
        $buyerRatingAvg = $user->purchases()
        ->where('status', Purchase::STATUS_COMPLETED)
            ->whereNotNull('seller_rating')
            ->avg('seller_rating');

        if (!is_null($buyerRatingAvg)) {
            $buyerRatingAvg = round($buyerRatingAvg, 1);
        }

        // 平均評価（出品側）
        $sellerRatingAvg = Purchase::query()
            ->whereHas('product', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', Purchase::STATUS_COMPLETED)
            ->whereNotNull('buyer_rating')
            ->avg('buyer_rating');

        if (!is_null($sellerRatingAvg)) {
            $sellerRatingAvg = round($sellerRatingAvg, 1);
        }

        return view('profile.show', [
            'user'              => $user,
            'currentTab'        => $currentTab,
            'myProducts'        => $myProducts,
            'tradingPurchases'  => $tradingPurchases,
            'purchases'         => $completedPurchases,
            'buyerRatingAvg'    => $buyerRatingAvg,
            'sellerRatingAvg'   => $sellerRatingAvg,
            'tradingUnreadTotal' => $tradingUnreadTotal,
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        $profile = Profile::firstWhere('user_id', $user->id);
        return view('profile.edit', compact('profile', 'user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // 画像差し替え（古いファイル削除 → 新規保存）
        if ($request->hasFile('profile_image')) {
            if (!empty($profile->profile_image)) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $profile->profile_image = $request->file('profile_image')
                ->store('profile_images', 'public');
        }
        $user->update(['name' => $validated['name']]);

        $profile->fill([
            'name'         => $validated['name'],
            'postal_code'  => $validated['postal_code'],
            'address'      => $validated['address'],
            'building'     => $validated['building'] ?? null,
        ])->save();

        // 初回ログインフラグ更新
        $wasFirst = (bool) $user->is_first_login;
        $user->update([
            'is_first_login'    => false,
            'profile_completed' => true,
        ]);

        return $wasFirst
            ? redirect()->route('products.index')->with('success', 'プロフィールを登録しました。')
            : redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました。');
    }

    // 購入履歴
    public function purchases()
    {
        $purchases = Purchase::with('product')
            ->where('user_id', auth()->id())
            ->latest('paid_at')
            ->get();

        return view('profile.purchases', compact('purchases'));
    }
}
