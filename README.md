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

7. メール送信設定（Mailtrap）
- 本アプリのメール通知機能では MailTrap を使用しています。
MailTrap のアカウントを作成し、Inbox をひとつ作成してください。

MailTrap の Inbox 画面に表示される SMTP Credentials を .env に反映します。

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=（Mailtrap の Username）
MAIL_PASSWORD=（Mailtrap の Password）
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=example@example.com # 任意のメールアドレスでOK
MAIL_FROM_NAME=“Freemarket”

3）取引完了時に送信される確認メールが Mailtrap の Inbox に届きます。

----

8. マイグレーションとシーディング
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

## 追加機能概要（取引チャット・評価機能）

以下の、フリマ取引機能を追加実装しました。

- 取引中の商品の一覧表示（マイページの「取引中の商品」タブ）
- 取引チャット機能
  - テキストメッセージ送信
  - 画像アップロード(jpeg / png 形式)
  - メッセージの編集・削除
- 取引完了機能
- 購入者が「取引完了」ボタンを押すと'purchases.status'を 'completed'に変更
- 取引完了後、完了メールを出品者に送信（MailTrap で確認可能）
- ユーザー評価機能
- 購入者・出品者がお互いを評価（１〜５）
- 評価は、'purchases.buyer_rating' / 'purchases.seller_rating' に保存
- プロフィール画面で「購入者としての平均評価」「出品者としての平均評価」を表示
- 未読メッセージ数
- 'trade_messages' と 'buyer_last_read_at' / 'seller_last_read_at' を用いて、
  マイページの「取引中の商品」タブに未読件数バッジを表示

---

・ダミーデータについて（Seeder）
本アプリでは検証用にユーザー・商品・カテゴリーのダミーデータを Seeder によって自動生成しています。
Doker 起動後、以下のコマンドでデータベースへ投入されます

・ 生成されるテストユーザー

Seeder によって以下の 3 ユーザーが作成されます。
メール認証済み状態（email_verified_at あり） のため、そのままログインできます。

① 出品者 A(評価なし)

- メール seller1@example.com
- パスワード password
- 商品 C001〜C005 を出品

② 出品者 B(評価あり)

- メール seller2@example.com
- パスワード password
- 商品 C006〜C010 を出品

③ 購入者ユーザー(評価あり)

- メール buyer@example.com
- パスワード password
- 商品は出品せず、購入専用ユーザー

- 購入した商品(１つ目)
  ・ 腕時計：価格：15,000 円
  ・ status = trading(取引中)

- 購入した商品(２つ目)
  ・ マイク：価格：8,000 円
  ・ status = completed(取引完了済み)
  ・ 'buyer_rating'    => 4,
  ・ 'seller_rating'   => 2,

- ログイン確認用
  • ログイン画面 → seller1@example.com / password
  • マイページ → 出品商品一覧で C001〜C005 が確認できます。
- 商品画像は storage/app/public/products/ へ、コピー済みで、
  php artisan storage:link により公開されます。
