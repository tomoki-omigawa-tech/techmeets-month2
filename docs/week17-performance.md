# Week 17 パフォーマンス改善記録

## 計測条件
- 対象: https://tomoki-omigawa.com/posts（本番・投稿0件）
- ブラウザ: Chrome シークレットウィンドウ
- Lighthouse: Mode Navigation / Device Mobile / Category Performance

## Lighthouse

| 指標 | Before | After |
|---|---|---|
| Performance | 97 | |
| FCP | 2.1 s | |
| LCP | 2.1 s | |
| TBT | 0 ms | |
| CLS | 0 | |
| Speed Index | 2.1 s | |
| TTFB | 120 ms | |

## Before時点の主な指摘
- Use efficient cache lifetimes（推定144 KiB削減）: 静的ファイルにキャッシュ期限が未設定
- Network dependency tree / 3rd parties: 外部フォント（fonts.bunny.net）の読み込み
- Reduce unused JavaScript（64 KiB）/ Reduce unused CSS（46 KiB）

## 補足
- TTFBは120msと十分速く、LCPの大半はブラウザ側の読み込み待ち
- いいね数は `likes_count` カラム（カウンターキャッシュ）で保持しているため、集計によるN+1は発生しない

## N+1問題の解消

投稿一覧に投稿者名を表示する際、投稿ごとに `users` テーブルへのSQLが発行されるN+1が発生した。
`PostRepository::paginate()` の eager loading に `user` を追加して解消。あわせて `user:id,name` で必要なカラムだけを取得するようにした。

計測方法: tinkerで一覧取得（10件）→ 各投稿のカテゴリ名・投稿者名にアクセスし、`DB::getQueryLog()` で発行SQL数を数えた（ローカル・投稿3000件）。

| | クエリ数 | 内訳 |
|---|---|---|
| Before | 13 | 件数取得1 + 投稿1 + カテゴリ1 + 投稿者10 |
| After | 4 | 件数取得1 + 投稿1 + カテゴリ1 + 投稿者1 |

変更内容:
- `app/Repositories/PostRepository.php`: `Post::with('category')` → `Post::with(['category', 'user:id,name'])`
- `resources/views/posts/index.blade.php`: 一覧に投稿者名を表示

## インデックスによるクエリ改善

投稿一覧の `Post::latest()->paginate(10)` が発行する `ORDER BY created_at DESC LIMIT 10` が、`created_at` にインデックスが無いため全件スキャン＋全件ソートになっていた。
マイグレーションで `posts.created_at` にインデックスを追加して改善。

対象SQL: `SELECT * FROM posts ORDER BY created_at DESC LIMIT 10`（ローカル・投稿3000件）

| | type | key | rows | Extra | 実行時間（100回平均） |
|---|---|---|---|---|---|
| Before | ALL | NULL | 2999 | Using filesort | 5.76 ms |
| After | index | posts_created_at_index | 10 | Backward index scan | 1.66 ms |

- 読み取り行数が 2999 → 10 に減り、ソート処理（filesort）が不要になった
- 実行時間は約71%短縮。データ件数が増えるほど差は大きくなる（Beforeは件数に比例して遅くなるが、Afterはほぼ一定）
- 変更: `database/migrations/2026_09_24_000000_add_created_at_index_to_posts_table.php`

## キャッシュ導入（Redis）

投稿一覧の取得結果を `Cache::remember()` で10分間キャッシュし、ローカルのキャッシュドライバーをRedisに変更した。

| | 時間 | クエリ数 |
|---|---|---|
| キャッシュなし（miss） | 906.11 ms | 4 |
| キャッシュあり（hit） | 18.20 ms | 0 |

- 計測: tinkerでキャッシュを空にしてから `PostService::getPosts()` を2回呼び出した（ローカル・投稿3000件）
- missの時間には、初回のDB接続確立なども含まれる。hitではDBへのアクセスが完全に無くなっている

### キャッシュの無効化

- 投稿の作成・更新・削除、いいね時に、一覧キャッシュのバージョン番号を上げて古いキャッシュを参照しないようにした（`Post::flushIndexCache()`）
- 教材の `Cache::tags()` はRedis・Memcached専用で、本番（`CACHE_STORE=database`）では例外になるため、どのドライバーでも動く「キーにバージョン番号を含める」方式を採用
- 確認: 新規投稿を作成すると、一覧の先頭が 1446 → 3001（新規投稿のID）に切り替わった

### 構成の変更
- `docker-compose.yml`: `redis:7-alpine` サービスを追加（外部公開はせず、アプリからのみ接続）
- `predis/predis` を追加（PHPイメージに phpredis 拡張が無いため）
- ローカル `.env`: `CACHE_STORE=redis` / `REDIS_CLIENT=predis` / `REDIS_HOST=redis`
- テストは `phpunit.xml` の `CACHE_STORE=array` のまま影響なし

## 静的ファイルのHTTPキャッシュ

Lighthouseの「Use efficient cache lifetimes」（推定144 KiB）への対策。
Viteのビルド成果物（`public/build/`）はファイル名にハッシュが付き、内容が変わればファイル名も変わるため、`Cache-Control: public, max-age=31536000, immutable` で1年間ブラウザにキャッシュさせても古いファイルが使われる心配がない。

- `docker/nginx/default.conf`: `location /build/` にキャッシュヘッダーを追加
- `.github/workflows/deploy.yml`: デプロイ時に `nginx -t`（設定チェック）と `nginx -s reload` を実行するよう追加

### 本番反映時のトラブルと対応

本番デプロイ後、キャッシュヘッダーが付かなかった。原因は、`docker-compose.yml` で nginx の設定ファイルを **ファイル単体でバインドマウント** していたこと。`git pull` はファイルを上書きせず新しいファイルに置き換えるため、コンテナ側は古いファイルを参照し続け、`nginx -s reload` しても設定が変わらなかった。

- 確認: ホスト側のファイルには設定があるが、コンテナ内のファイルには無かった
- 対応: `docker compose restart nginx` でマウントし直して反映。`deploy.yml` も reload から restart に変更
