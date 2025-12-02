<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');

        $currentTab = $request->query('tab', 'selling');

        $myProducts = $user->products()
            ->latest()
            ->get();

        $tradingPurchases = $user->purchases()
            ->with('product')
            ->where('status', Purchase::STATUS_TRADING)
            ->latest('created_at')
            ->get();

        $completedPurchases = $user->purchases()
            ->with('product')
            ->where('status', Purchase::STATUS_COMPLETED)
            ->latest('paid_at')
            ->get();

        $buyerRatingAvg = $user->purchases()
            ->whereNotNull('buyer_rating')
            ->avg('buyer_rating');

        if (!is_null($buyerRatingAvg)) {
            $buyerRatingAvg = round($buyerRatingAvg, 1);
        }

        $sellerRatingAvg = Purchase::query()
            ->whereHas('product', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereNotNull('seller_rating')
            ->avg('seller_rating');

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
