# Laravel Docker 開発環境

Docker を使って構築した Laravel の開発環境です。
Nginx + PHP-FPM + MySQL + phpMyAdmin の構成で動作します。

## 必要なもの

- Docker Desktop

## 構成

| サービス   | 役割                   | ポート |
| ---------- | ---------------------- | ------ |
| nginx      | Web サーバー           | 80     |
| app        | PHP-FPM（アプリ本体）  | -      |
| db         | MySQL データベース     | 3306   |
| phpmyadmin | データベース管理ツール | 8080   |

## セットアップ手順

1. リポジトリをクローンする

```bash
   git clone https://github.com/tomoki-omigawa-tech/techmeets-month2.git
   cd techmeets-month2
```

2. `.env` ファイルを用意する

```bash
   cp .env.example .env
```

3. コンテナをビルド・起動する

```bash
   docker compose up -d --build
```

4. アプリケーションキーを生成する

```bash
   docker compose exec app php artisan key:generate
```

5. マイグレーションを実行する

```bash
   docker compose exec app php artisan migrate
```

## アクセス先

- アプリ: http://localhost
- phpMyAdmin: http://localhost:8080 （ユーザー: `root` / パスワード: `secret`）

## ポート番号の変更

ポート番号は `.env` で管理しています。
別のポートを使いたい場合は `.env` の以下の値を変更してください。

```env
NGINX_PORT=80
DB_PORT_HOST=3306
PMA_PORT=8080
```

## よく使うコマンド

```bash
# コンテナ起動
docker compose up -d

# コンテナ停止
docker compose stop

# コンテナの中に入る
docker compose exec app bash

# Artisan コマンド実行（例：マイグレーション）
docker compose exec app php artisan migrate
```

