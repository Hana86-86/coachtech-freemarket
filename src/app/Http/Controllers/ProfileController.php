<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');

        $myProducts = $user->products()->latest()->get();

        $purchases = $user->purchases()
            ->with('product')
            ->latest('paid_at')
            ->get();

        return view('profile.show', [
            'user'    => $user,
            'myProducts' => $myProducts,
            'purchases'  => $purchases,
        ]);
    }

    public function edit()
    {
        $profile = Profile::firstWhere('user_id', auth()->id());
        return view('profile.edit', compact('profile'));
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