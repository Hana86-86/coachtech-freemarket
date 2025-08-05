# coachtech-freemarket

・COACHTECH フリマ（フリマアプリ）
ユーザー登録すると商品を出品できるようになるフリマアプリです。
Laravel 10 ＋　 Fortify を使用し、Docker 環境で動作します。

・使用技術

- PHP 8.2
- Laravel 10
- Laravel Fortify
- MySQL 8.0
- Nginx
- phpAdmin
- Docker / Docker Compose

・環境構築手順

1. git clone git@github.com:ユーザー名/coachtech-freemarket.git

- cd coachtech-freemarket

2. Laravel プロジェクト作成

- composer create-project laravel/laravel="10.\*" src

3. 環境変数設定

- cp src/.env.example src/.env
- .env 修正
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=laravel_db
  DB_USERNAME=laravel_user
  DB_PASSWORD=laravel_pass

4. MySQL のイメージを ARM64 対応版に変更

- docker-compose.yml の mysql サービスに以下を追加
- platform: linux/amd64

5. Docker コンテナ起動

- docker compose up -d --build

6. アプリキー生成

- docker compose exec php bash
- cd src
- php artisan key:generate

7. LaravelFortify インストール

- composer require laravel/fortify

8. FortifyServiceProvider 作成

- php artisan make:provider FortifyServiceProvider
  作成後、config/app.php の providers に以下追記
- App\Providers\FortifyServiceProvider::class,

9. マイグレーション実行

- php artisan migrate

![ER図](image.png)

---

・エラーと解決方法

1. Class "App\Providers\FortifyServiceProvider" not found
   原因: config/app.php に FortifyServiceProvider を追記しているのに
   ファイルが存在しない状態で artisan コマンドを実行していた
   解決: 1. config/app.php の追記を一旦コメントアウト 2. php artisan make:provider FortifyServiceProvider 3. ファイル生成後に config/app.php へ再追加

2. Your Composer dependencies require a PHP version ">= 8.2.0". You are running 8.1.33
   原因：PHP バージョンエラー
   解決：Dockerfile を PHP 8.2 に修正
   FROM php:8.2-fpm
   再ビルド：
   docker compose build --no-cache
   docker compose up -d

---

・エラーと解決方法

1.419 エラー(Page Expired)
・ログインフォーム送信時に発生
・メッセージ：419 Page Expired

2.404 エラー
・ログイン成功後に/home にリダイレクトされるが、そのルートが存在しないため発生
・メッセージ：NotFound

原因： .env が http://localhost のまま、アクセスは http://localhost:8081
・Laravel Fortify がデフォルトで /home にリダイレクト
・今回のアプリには /home ルートが無いため 404

419エラー解決方法：.env を以下に修正

- APP_URL=http://localhost:8081
  修正後にキャッシュクリア
- docker compose exec php php artisan config:clear
- docker compose exec php php artisan cache:clear
- docker compose exec php php artisan route:clear

404エラー解決方法：
- LoginResponse をオーバーライドしてログイン後 /items にリダイレクトする。
ファイル作成：src/app/Http/Responses/LoginResponse.php
```
<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return redirect()->intended('/items');
    }
}
```
- サービスプロバイダに登録
src/app/Providers/AppServiceProvider.php
```
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Responses\LoginResponse as CustomLoginResponse;

public function register()
{
    $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
}
```
----

・エラーと解決方法
1.プロフィール初回登録画面に遷移しない問題
- 原因: `is_first_login` フラグが更新されない。
- 対応:
  - `LoginResponse.php` を修正し、`is_first_login` が true の場合に `profile.create` に遷移。
  - `ProfileController@store` でプロフィール保存後に `is_first_login` を false に更新。
  - 修正後に `php artisan optimize:clear` を実行しキャッシュをクリア。

```php
// ProfileController@store
$request->user()->update(['is_first_login' => false]);

2. メール認証後に items へ遷移する問題
- 原因: Fortify のメール認証処理に初回ログイン判定が組み込まれていなかった。
- 対応:
- web.php のメール認証ルートに is_first_login 判定を追加。
```php
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    if ($request->user()->is_first_login) {
        return redirect()->route('profile.create');
    }
    return redirect()->route('items.index');
})->middleware(['auth', 'signed'])->name('verification.verify');
```
---
メール認証の確認方法

Mailtrap の利用
	•本番メールサーバーの代わりに Mailtrap を使用。
- .env 設定:
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxx
MAIL_PASSWORD=xxxxx
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Freemarket"

手順
	1.	新規会員登録を行う。
	2.	Mailtrap の Inbox に送信された認証メールを確認。
	3.	メール内の認証リンクをクリック。
	4.	初回ログイン時はプロフィール登録画面に遷移、その後は items 一覧に遷移。


