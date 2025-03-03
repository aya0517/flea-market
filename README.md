# flea-market

```
git clone git@github.com:tamachima327/laravel-template.git
```

```
yes | rm -r laravel-template/.git
```

```
git clone 新しいリポジトリのSSH
```

```
mv laravel-template/* laravel-template/.[^\.]* 新しいリポジトリの名前
```

```
rm -r laravel-template
```

```
cd 新しいリポジトリの名前
```

```
code .
```

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

## 環境構築手順が終わった後にやること(この手順はアプリ完成時には README から削除する)

-   ブラウザで動作チェック  
    localhost にアクセスして動作確認  
    localhost:8080 にアクセスして phpmyadmin が見れるか確認

-   環境構築手順で動くことを確認したら commit/push して環境構築完了  
    コミットメッセージは"First commit"

