# coachtech-freemarket

・COACHTECH フリマ（フリマアプリ）
ユーザー登録すると商品を出品できるようになるフリマアプリです。
検索 UI/ロジックを共通化して、商品一覧とマイリスト（お気に入り）で同じ操作感を実現。
Laravel 10 ＋　 Fortify を使用し、Docker 環境で動作します。

・使用技術

- PHP 8.2
- Laravel 10
- Laravel Fortify
- MySQL 8.0
- Nginx
- phpMyAdmin
- Docker / Docker Compose

・環境構築手順

1）リポジトリをクローン
git clone git@github.com:Hana86-86/coachtech-freemarket.git

cd coachtech-freemarket

2） 環境変数設定

cp src/.env.example src/.env

- .env を開いて以下を確認、修正
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=laravel_db
  DB_USERNAME=laravel_user
  DB_PASSWORD=laravel_pass

３） Docker コンテナ起動

docker compose up -d --build

４）PHP コンテナに入る

docker compose exec php bash
cd /var/www/src

５）依存関係インストール

composer install

6. アプリキー生成とストレージリンク

php artisan key:generate

php artisan storage:link

７） マイグレーションとシーディング
・ダミーデータについて（Seeder）
本アプリでは検証用にユーザー・商品・カテゴリーのダミーデータを Seeder によって自動生成しています。
Docker 起動後、以下のコマンドでデータベースへ投入されます

php artisan migrate:fresh --seed

動作確認 URL
• Web: http://localhost:8081
• phpMyAdmin: http://localhost:8080

---

![ER図](er-diagram.png)

---

・ダミーデータについて（Seeder）
本アプリでは検証用にユーザー・商品・カテゴリーのダミーデータを Seeder によって自動生成しています。
Doker 起動後、以下のコマンドでデータベースへ投入されます

・ 生成されるテストユーザー

Seeder によって以下の 3 ユーザーが作成されます。
メール認証済み状態（email_verified_at あり） のため、そのままログインできます。

① 出品者 A
seller1@example.com
password
商品 C001〜C005 を出品

② 出品者 B
seller2@example.com
password
商品 C006〜C010 を出品

③ 購入者ユーザー
buyer@example.com
password
商品は出品せず、購入専用ユーザー

- ログイン確認用
  • ログイン画面 → seller1@example.com / password
  • マイページ → 出品商品一覧で C001〜C005 が確認できます。
- 商品画像は storage/app/public/products/ へコピー済みで、
  php artisan storage:link により公開されます。
