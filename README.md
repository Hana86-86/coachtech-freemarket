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

php artisan migrate --seed

動作確認 URL
• Web: http://localhost:8081
• phpMyAdmin: http://localhost:8080

---

![ER図](er-diagram.png)

---
