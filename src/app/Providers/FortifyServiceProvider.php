<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ユーザー登録処理
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        // LoginResponse を Fortify に差し替え
        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        // RegisterResponse を Fortify に差し替え
        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            \App\Http\Responses\RegisterResponse::class
        );
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

    // 会員登録画面
    Fortify::registerView(function () {
        return view('auth.register');
    });
    Event::listen(Registered::class, function ($event) {
        // 登録直後はログアウトさせる
        auth()->logout();

    Event::listen(Registered::class, function ($event) {
    auth()->logout();
    return redirect()->route('login'); // 明示的にログイン画面へ
});

        // メール認証画面へ
        session()->flash('status', '登録ありがとうございます。メールを確認してください。');
    });

    // 正しい登録処理の指定
    Fortify::createUsersUsing(CreateNewUser::class);

    // 認証処理
    Fortify::authenticateUsing(function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }
        return null;
    });

    Fortify::verifyEmailView(function () {
    return view('auth.verify-email');
});

}
}