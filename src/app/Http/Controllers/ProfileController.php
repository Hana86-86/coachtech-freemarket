<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    public function create()
    {
        $profile = \App\Models\Profile::firstWhere('user_id', auth()->id());
        return view('profile.create', compact('profile'));
    }
    public function purchases()
    {
        $purchases = Purchase::with('product')
            ->where('user_id', auth()->id())
            ->latest('paid_at')
            ->get();
        return view('profile.purchases', compact('purchases'));
    }


    public function store(ProfileRequest $request)
    {
    $user = $request->user();
    $wasFirst = (bool) $user->is_first_login;
    $path = null;

    // 画像アップロード（差し替え時は旧画像を削除）
    $path = null;
    if ($request->hasFile('profile_image')) {
        // 既存があれば削除
        if (!empty($user->profile?->profile_image)) {
            Storage::disk('public')->delete($user->profile->profile_image);
        }

        $img = Image::read($request->file('profile_image'));
        // 回転補正（v3 / v2 互換）
        if (method_exists($img, 'orient')) {
        $img->orient();          // v3
        } elseif (method_exists($img, 'orientate')) {
        $img->orientate();       // v2
        }
        // 最大800px アスペクト維持・拡大しない
        $img->resize(1200, 1200, fn($c) => $c->aspectRatio()->upsize());
        // webpで試し、ダメならjpeg にフォールバック
        try {
            $binary = (string) $img->encodeByExtension('webp', quality: 92);
            $filename = 'profiles/'.Str::uuid().'.webp';
        } catch (\Throwable $e) {
            $binary = (string) $img->encodeByExtension('jpg', quality: 85);
            $filename = 'profiles/'.Str::uuid().'.jpg';
        }

            Storage::disk('public')->put($filename, $binary);
            $path = $filename;
    }

    // プロフィール作成 or 更新
    $profile = Profile::updateOrCreate(
        ['user_id' => $user->id],
        [
            'name'         => $request->input('name'),
            'postal_code'  => $request->input('postal_code'),
            'address'      => $request->input('address'),
            'building'     => $request->input('building'),
            // 画像を変更した時のみ差し替え
            'profile_image'=> $path ?? ($user->profile->profile_image ?? null),
        ]
    );

    // 初回ログインフラグを false に更新
    $request->user()->update([
        'is_first_login' => false,
        'profile_completed' => true,
    ]);

    return $wasFirst
        ? redirect()->route('products.index')->with('success', 'プロフィールを登録しました。')
        : redirect()->route('profile.create')->with('success', 'プロフィールを更新しました。');
}
    public function destroyAvatar()
    {
        $user = auth()->user();
        if ($user->profile?->profile_image) {
        Storage::disk('public')->delete($user->profile->profile_image);
        $user->profile->update(['profile_image' => null]);
    }
    return back()->with('success', 'プロフィール画像を削除しました');
    }
}