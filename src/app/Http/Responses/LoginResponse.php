<?php

namespace App\Http\Responses;


use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {

    $user = auth::user();

    //メール認証チェック
    if (! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }
    //プロフィール未登録チェック
    if ($user->is_first_login || !$user->profile_completed) {
    return redirect()->route('profile.edit');
}

    // 通常ログイン（商品一覧へ）
    return redirect()->route('products.index');
}
}