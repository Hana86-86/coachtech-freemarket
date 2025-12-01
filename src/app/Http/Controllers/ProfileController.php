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
        // ① ログインユーザー + プロフィールを取得
        $user = $request->user()->load('profile');

        // ② どのタブを開くか（?tab=... が無ければ 'selling' をデフォルトに）
        $currentTab = $request->query('tab', 'selling');

        // ③ 出品した商品（自分が出品者）
        $myProducts = $user->products()
            ->latest()
            ->get();

        // ④ 取引中の購入（status = trading）
        $tradingPurchases = $user->purchases()
            ->with('product')                  // 商品情報を一緒に取得
            ->where('status', Purchase::STATUS_TRADING)
            ->latest('created_at')
            ->get();

        // ⑤ 購入が完了した商品（status = completed）
        $completedPurchases = $user->purchases()
            ->with('product')
            ->where('status', Purchase::STATUS_COMPLETED)
            ->latest('paid_at')
            ->get();

        // ⑥ ビューに渡す
        return view('profile.show', [
            'user'              => $user,
            'currentTab'        => $currentTab,        // ★ どのタブか
            'myProducts'        => $myProducts,        // 出品した商品
            'tradingPurchases'  => $tradingPurchases,  // 取引中
            'purchases'         => $completedPurchases, // 完了した購入
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
