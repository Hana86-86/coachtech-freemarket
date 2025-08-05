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
    if (! $user->is_first_login) {
        return redirect()->route('profile.create');
    }

    // 通常ログイン（商品一覧へ）
    return redirect()->route('items.index');
}
}