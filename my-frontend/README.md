# Week10 基本課題：LaravelアプリをAPI化してフロントエンドから表示する

Week9で作成したLaravelブログアプリ（Service層・Eloquentリレーション実装済み）にAPIエンドポイントを追加し、Reactフロントエンドから呼び出して投稿一覧を表示しました。

## 動かし方

### バックエンド（Laravel）

1. リポジトリのルート（`laravel-docker-app`）でDockerを起動します。

```bash
docker compose up -d
```

2. `http://localhost/api/posts` にアクセスすると、投稿一覧がJSON形式で取得できます。

### フロントエンド（React）

1. `my-frontend` ディレクトリに移動します。

```bash
cd my-frontend
```

2. 依存パッケージをインストールします。

```bash
npm install
```

3. 開発サーバーを起動します。

```bash
npm run dev
```

4. ブラウザで `http://localhost:5173` にアクセスすると、投稿一覧が表示されます。

## 実装内容

- `routes/api.php` に `GET /api/posts` エンドポイントを追加
- `Api\PostController` で `PostService` を利用し、投稿一覧を取得
- `PostResource` でレスポンス形式（id, title, body, user, category, created_at, updated_at）を統一
- React側では `axios` を使って `/api/posts` にGETリクエストを送信し、`PostList` → `PostItem` コンポーネントで一覧表示
- `config/cors.php` を publish し、フロントエンドからのアクセスを許可
