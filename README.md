# ブログシステム (Week7 課題)

LaravelのMVCパターンで作成したブログシステムです。投稿のCRUD機能、カテゴリー分類、ページネーション、バリデーションを実装しています。

## 機能一覧

- 投稿一覧表示（ページネーション付き、1ページ10件）
- 投稿詳細表示
- 投稿作成（タイトル・内容・カテゴリー）
- 投稿編集
- 投稿削除（確認ダイアログ付き）
- バリデーション（必須チェック・文字数制限・カテゴリー存在チェック）
- Bladeレイアウト継承による共通レイアウト

## 使用技術

- Laravel (PHP 8.2)
- MySQL 8.0
- Nginx
- Docker / Docker Compose
- Blade テンプレート

## テーブル定義

### categories テーブル

| カラム名 | 型 | 説明 |
|---|---|---|
| id | bigint (PK) | カテゴリーID |
| name | varchar(255) | カテゴリー名 |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### posts テーブル

| カラム名 | 型 | 説明 |
|---|---|---|
| id | bigint (PK) | 投稿ID |
| title | varchar(255) | タイトル |
| body | text | 本文 |
| category_id | bigint (FK) | カテゴリーID（categories.id を参照） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### リレーション

- Post は1つの Category に属する（belongsTo）
- Category は複数の Post を持つ（hasMany）
- カテゴリー削除時、関連する投稿も削除される（cascade）

## セットアップ

\`\`\`bash
# コンテナ起動
docker compose up -d

# マイグレーション
docker compose exec app php artisan migrate

# テストデータ投入
docker compose exec app php artisan db:seed
\`\`\`

ブラウザで http://localhost にアクセス

## スクリーンショット

### 投稿一覧
（スクリーンショットをここに追加）

### 投稿詳細
（スクリーンショットをここに追加）

### 投稿作成
（スクリーンショットをここに追加）
