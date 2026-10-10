# 練習課題3：ToDoアプリ RESTful API仕様書

## 共通事項
- ベースURL：`/api`
- 認証：すべてのエンドポイントで必要（`Authorization: Bearer {token}`）
- 自分のToDoのみ操作可能（他人のToDoは 403 Forbidden）
- 形式：リクエスト・レスポンスともに JSON

## エンドポイント一覧
| メソッド | パス | 説明 |
|---|---|---|
| GET | /api/todos | ToDo一覧取得 |
| GET | /api/todos/{id} | ToDo詳細取得 |
| POST | /api/todos | ToDo作成 |
| PATCH | /api/todos/{id} | ToDo更新 |
| DELETE | /api/todos/{id} | ToDo削除 |

---

## GET /api/todos（一覧取得）

クエリパラメータ
- completed (boolean, optional)：完了状態で絞り込み
- page (integer, optional)：ページ番号（デフォルト 1）

レスポンス例（200 OK）
```json
{
  "data": [
    { "id": 1, "title": "牛乳を買う", "completed": false, "due_date": "2026-10-01", "created_at": "2026-09-28T10:00:00Z" }
  ],
  "meta": { "current_page": 1, "total": 1, "per_page": 20 }
}
```

## GET /api/todos/{id}（詳細取得）

レスポンス例（200 OK）
```json
{ "data": { "id": 1, "title": "牛乳を買う", "description": "低脂肪のもの", "completed": false, "due_date": "2026-10-01", "created_at": "2026-09-28T10:00:00Z", "updated_at": "2026-09-28T10:00:00Z" } }
```

エラー（404 Not Found）
```json
{ "message": "Todo not found." }
```

## POST /api/todos（作成）

リクエストボディ
```json
{ "title": "牛乳を買う", "description": "低脂肪のもの", "due_date": "2026-10-01" }
```

バリデーション
- title：必須、1〜100文字
- description：任意、1000文字以内
- due_date：任意、日付形式（YYYY-MM-DD）

レスポンス例（201 Created）
```json
{ "data": { "id": 2, "title": "牛乳を買う", "description": "低脂肪のもの", "completed": false, "due_date": "2026-10-01", "created_at": "2026-09-28T11:00:00Z" } }
```

エラー（422 Unprocessable Entity）
```json
{ "message": "The given data was invalid.", "errors": { "title": ["The title field is required."] } }
```

## PATCH /api/todos/{id}（更新）

リクエストボディ（変更したい項目のみ）
```json
{ "completed": true }
```

レスポンス例（200 OK）
```json
{ "data": { "id": 2, "title": "牛乳を買う", "completed": true, "updated_at": "2026-09-28T12:00:00Z" } }
```

## DELETE /api/todos/{id}（削除）

レスポンス：204 No Content（ボディなし）

## ステータスコード一覧
| コード | 意味 |
|---|---|
| 200 | 成功 |
| 201 | 作成成功 |
| 204 | 削除成功 |
| 401 | 未認証 |
| 403 | 他人のToDoへのアクセス |
| 404 | 存在しない |
| 422 | バリデーションエラー |
