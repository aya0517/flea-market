# flea-market

## プロジェクト概要
Laravelを使用して構築されたシンプルなフリーマーケットアプリケーションである。
ユーザーは商品を出品・閲覧・購入でき、カテゴリごとの検索やコメント、いいね機能などもついている。

- 主な機能：
・会員登録／ログイン／ログアウト機能
・商品の一覧表示／詳細ページ／出品機能
・商品の編集／削除（出品者のみ）
・カテゴリ検索機能
・商品へのコメント投稿
・商品のお気に入り（いいね）機能
・商品の購入処理
・管理者ページ（管理者のみアクセス可）

## 環境構築手順

-   コンテナを立ち上げるため、以下を実行
```
docker compose up -d --build
```

-   env ファイルの作成をするため、以下を実行
```
cp src/.env.example src/.env
```

-   php にコンテナに入るため、以下を実行
```
docker compose exec php bash
```

-   composer パッケージをインストールするため、以下を実行
```
composer install
```

-   アプリケーションキーを作成するため、以下を実行
```
php artisan key:generate
```

-   マイグレーションを実行するため、以下を実行
```
php artisan migrate
```

-   シーダーを実行するため、以下を実行
```
php artisan db:seed
```

-   サーバーを起動するため、以下を実行
```
php artisan serve
```

-   env.ファイルの以下の部分を、編集してください  
STRIPE_SECRETキーを変更してください　　


## 使用技術

- バックエンド
・Laravel 8.x: PHPフレームワーク（アプリケーション全体の構築）

・Laravel Fortify: ユーザー認証・メール認証などのセキュリティ機能

・Laravel Sanctum: APIトークン認証（将来的なAPI対応に備えて）

・Laravel Tinker: CLIでのモデル操作・デバッグ用

・Doctrine DBAL: テーブル構造変更に対応するため

・Guzzle: 外部HTTP通信ライブラリ（例：外部API利用時）

・Stripe PHP SDK: 決済機能の実装に使用

- フロントエンド
・Bladeテンプレート: Laravel標準のテンプレートエンジン

・Laravel Mix (v6): フロントエンドアセットのビルドとバンドル

・Axios: フロントエンドからのHTTP通信（非同期リクエスト）

・PostCSS: CSSの構文変換や最適化処理

・Lodash: JavaScriptユーティリティライブラリ

- 認証・セキュリティ
・メール認証機能（Fortifyを通じて）

・ログイン／ログアウト／新規登録

・初回ログイン時にプロフィール編集へ誘導

・認証付きルートグループでマイページ・出品・購入を保護

- 決済機能
・Stripe を使った決済処理（購入 → 成功／キャンセル処理）

・配送先の編集、購入履歴など購入フローも完備

- テスト関連
・PHPUnit


## ログイン情報
- 管理者ユーザー
・メールアドレス: admin@example.com

・パスワード: password123

- 一般ユーザー
・メールアドレス: test@example.com

・パスワード: password123
