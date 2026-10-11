# techmeets Month 2（Week 5〜9）＋ ブログアプリの開発リポジトリ

techmeets の Month 2 の課題と、Week 7 から作り続けている Laravel ブログアプリの開発リポジトリです。
本番環境（https://tomoki-omigawa.com ）へは、このリポジトリの main から GitHub Actions で自動デプロイしています。

## Month 2（Week 5〜9）
| Week | テーマ | 内容 | 場所 |
|---|---|---|---|
| Week 5 | RESTful API 理解＋GitHub 実践 | Issue → ブランチ → PR → マージの流れ、Postman で HTTP メソッドの確認 | [week5-practice](https://github.com/tomoki-omigawa-tech/week5-practice) |
| Week 6 | Docker 基礎＋Laravel 環境構築 | nginx + PHP-FPM + MySQL + phpMyAdmin の Docker 環境で Laravel を構築 | このリポジトリ [PR #1](../../pull/1) |
| Week 7 | Laravel 基礎＋MVC モデル理解 | ブログシステム（CRUD・カテゴリー・ページネーション・バリデーション） | このリポジトリ [PR #2](../../pull/2) |
| Week 8 | Laravel 認証＋セキュリティ | Breeze による認証・認可（会員制ブログ）／PHP で作る認証付き掲示板 | このリポジトリ [PR #3](../../pull/3)／[techmeets-month1 の week8-bbs](https://github.com/tomoki-omigawa-tech/techmeets-month1/tree/main/week8-bbs) |
| Week 9 | 設計・責務分離＋DB 設計の深化 | Repository / Service パターンでのリファクタリング、Fat Controller 比較ドキュメント、タスク管理アプリ（練習課題1） | このリポジトリ [PR #4](../../pull/4)・[PR #5](../../pull/5)／[techmeets-task-manager](https://github.com/tomoki-omigawa-tech/techmeets-task-manager) |

## Month 3 以降
Week 10 以降もこのリポジトリで開発を続けたため、各 Month の時点のものを別リポジトリに整理しています。
| Month | 週 | リポジトリ |
|---|---|---|
| Month 1 | Week 1〜4 | [techmeets-month1](https://github.com/tomoki-omigawa-tech/techmeets-month1) |
| Month 3 | Week 10〜13（React 連携・AWS・Stripe / SendGrid・独自ドメイン / HTTPS） | [techmeets-month3](https://github.com/tomoki-omigawa-tech/techmeets-month3) |
| Month 4 | Week 14〜17（テスト・CI/CD・Docker 応用・パフォーマンス最適化） | [techmeets-month4](https://github.com/tomoki-omigawa-tech/techmeets-month4) |
| Month 5 | Week 18〜21（要件定義・設計・IT ビジネス・総合演習準備） | [techmeets-month5](https://github.com/tomoki-omigawa-tech/techmeets-month5) |
| 総合プロジェクト | Week 21〜24 | [daikolink](https://github.com/tomoki-omigawa-tech/daikolink) |

---

# ブログシステム（Week7〜8 課題）

LaravelのMVCパターンで作成したブログシステムです。投稿のCRUD機能、カテゴリー分類、ページネーション、バリデーションに加え、Week8でLaravel Breezeによる認証・認可機能を実装しました。

## 機能一覧

- 投稿一覧表示（ページネーション付き、1ページ10件）
- 投稿詳細表示
- 投稿作成（タイトル・内容・カテゴリー）
- 投稿編集
- 投稿削除（確認ダイアログ付き）
- バリデーション（必須チェック・文字数制限・カテゴリー存在チェック）
- Bladeレイアウト継承による共通レイアウト
- **ユーザー登録・ログイン（Laravel Breeze）**
- **ログインユーザーのみ投稿可能**
- **自分の投稿のみ編集・削除可能（他人の投稿を編集・削除しようとすると403エラー）**
- **投稿一覧・詳細は未ログインでも閲覧可能**

## 使用技術

- Laravel (PHP 8.2)
- Laravel Breeze（認証）
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
| user_id | bigint (FK) | 投稿者ID（users.id を参照） |
| title | varchar(255) | タイトル |
| body | text | 本文 |
| category_id | bigint (FK) | カテゴリーID（categories.id を参照） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### リレーション

- Post は1つの Category に属する（belongsTo）
- Post は1人の User に属する（belongsTo）
- Category は複数の Post を持つ（hasMany）
- カテゴリー削除時、関連する投稿も削除される（cascade）

## セットアップ

```bash
# コンテナ起動
docker compose up -d

# アプリケーションキーを生成
docker compose exec app php artisan key:generate

# マイグレーション
docker compose exec app php artisan migrate

# テストデータ投入（任意）
docker compose exec app php artisan db:seed
```

ブラウザで http://localhost にアクセス

## Week 8: 実装機能（会員制ブログ）

Laravel Breeze を使用して、匿名だった投稿機能に認証・認可を追加しました。

### 追加したファイル

- `database/migrations/*_create_categories_table.php`
- `database/migrations/*_create_posts_table.php`（`user_id` で投稿者を管理）
- `app/Models/Category.php`, `app/Models/Post.php`
- `app/Http/Controllers/PostController.php`（認可チェック実装）
- `resources/views/posts/*.blade.php`
- `routes/web.php`（投稿関連ルートを追加）

### セキュリティ

SQLインジェクション・XSS・CSRF・認可バイパスについてテストを実施しました。詳細は [`docs/security-report.pdf`](docs/security-report.pdf) を参照してください。

なお、認証機能を手動実装する練習として、別リポジトリ（`techmeets/month1/week8-bbs`）に PHP + PDO ベースの匿名掲示板も作成しています（練習課題1）。

## Week 11: AWSへのデプロイとS3連携

Week 9で完成したLaravel + Dockerアプリを、AWS EC2 + RDS + S3を使って本番相当の環境にデプロイしました。

### インフラ構成

- **EC2**: t3.micro (Ubuntu 24.04 LTS) — Webサーバー・アプリケーションサーバー
- **RDS**: db.t4g.micro (MySQL 8.0) — マネージドデータベース、EC2からのみアクセス可能
- **S3**: 画像アップロード用のオブジェクトストレージ
- **Docker Compose**: nginx + PHP-FPM のコンテナ構成（DBはRDSに移行したためdbコンテナは削除）

### セキュリティグループの設計

#### EC2用セキュリティグループ

| タイプ | プロトコル | ポート | ソース | 理由 |
|---|---|---|---|---|
| SSH | TCP | 22 | 自分のIPアドレス/32 | サーバー管理は自分だけが行うため、SSH接続元を自分のIPのみに制限。全世界に開放するとブルートフォース攻撃の標的になるリスクがある |
| HTTP | TCP | 80 | 0.0.0.0/0 | Webアプリケーションとして誰でも閲覧できる必要があるため全許可。ログイン等の機密操作はアプリケーション側の認証・認可で保護している |

#### RDS用セキュリティグループ

| タイプ | プロトコル | ポート | ソース | 理由 |
|---|---|---|---|---|
| MySQL/Aurora | TCP | 3306 | EC2のセキュリティグループ | データベースはアプリケーションサーバー（EC2）からのみアクセスできればよく、インターネットから直接接続できる必要はない。RDS自体もパブリックアクセスを「なし」に設定し、二重に保護している |

「すべて開放」「とりあえず許可」ではなく、実際にアクセスする必要がある送信元だけに絞ることで、不要な攻撃経路をなくす設計にしている。

### S3画像アップロード機能（練習課題1）

`/s3upload` にアクセスすると、画像をS3バケットにアップロードできる。

- バケット名: `week11-uploads-omitomo2026`
- IAMユーザーに `AmazonS3FullAccess` ポリシーをアタッチし、最小限のプログラムアクセスのみを許可
- アップロードされた画像はバケットポリシーにより公開読み取り可能（`s3:GetObject` のみ許可、書き込みはIAM認証されたアプリケーションからのみ）

### デプロイ手順

```bash
# EC2にSSH接続
ssh -i week11-key.pem ubuntu@<EC2のIP>

# リポジトリをclone
git clone https://github.com/tomoki-omigawa-tech/techmeets-month2.git
cd techmeets-month2

# .envを設定（DB_HOSTにRDSエンドポイント、AWS_*にS3の認証情報を設定）
cp .env.example .env
nano .env

# Composerで依存パッケージをインストール
docker compose exec app composer install

# コンテナ起動
docker compose up -d

# アプリケーションキー生成・マイグレーション
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

ブラウザで `http://<EC2のIP>` にアクセスして動作確認。

## Week 13: 動作確認コマンド

### `dig tomoki-omigawa.com +short`
ドメインに設定されたAレコードの値（EC2のElastic IPアドレス `13.193.140.67`）が返ってくる。DNSが正しく名前解決できていることを確認できる。

### `curl -I https://tomoki-omigawa.com`
HTTPSでアクセスした際のレスポンスヘッダーを表示する。`HTTP/1.1 200 OK`が返り、HTTPS化とアプリの正常動作を確認できる。

### `sudo certbot certificates`
取得済みのSSL証明書の一覧と有効期限を表示する。証明書が有効で、自動更新の対象になっていることを確認できる。
