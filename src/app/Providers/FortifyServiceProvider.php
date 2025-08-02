<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 新規登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // パスワードリセット関連
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forget-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password',['request' => $request]);
        });
    }
}
