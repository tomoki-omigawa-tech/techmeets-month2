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

## 練習課題1：新規作成フォームのAPI連携

`PostForm` コンポーネントを作成し、タイトル・本文・カテゴリIDを入力して投稿できるようにしました。送信時に `axios.post()` で `/api/posts` にリクエストを送り、成功したら親コンポーネント（App.jsx）に通知して投稿一覧を再取得・再表示しています。

※ 本来はログインユーザーの投稿として作成する設計ですが、今回はReact側に認証機能が未実装のため、開発用の暫定対応として `POST /api/posts` を認証なしでアクセス可能にし、投稿者IDは仮の値（id=2）を割り当てています。本番運用する場合は `auth:sanctum` ミドルウェアを再度有効化し、ログイン機能と連携させる必要があります。

## 練習課題2：コンポーネント分割の設計

投稿機能を `PostForm`（新規作成フォーム）、`PostList`（投稿一覧の表示）、`PostItem`（1件分の投稿表示）に分割しました。フォームと一覧表示は役割が明確に異なるため別コンポーネントとし、さらに一覧表示の中でも「複数件をループ処理する部分（PostList）」と「1件の見た目を組み立てる部分（PostItem）」を分けることで、将来的に投稿カードのデザインだけを変更したい場合や、一覧の並び替え・絞り込みロジックを追加したい場合に、影響範囲を局所化できるようにしています。データ取得（axios通信）や状態管理は親であるApp.jsxに集約し、子コンポーネントは受け取ったデータを表示する役割に専念させています。
