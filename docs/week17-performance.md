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
