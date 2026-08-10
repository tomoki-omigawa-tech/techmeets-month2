# 練習課題2: リファクタリング演習

基本課題で実際に行った `PostController` のリファクタリングを、Before/After形式でまとめる。

## Before（リファクタリング前・Fat Controller）

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['user_id'] = auth()->id();
        Post::create($validated);

        return redirect()->route('posts.index')->with('success', '投稿を作成しました。');
    }

    public function edit(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を編集する権限がありません。');

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を編集する権限がありません。');

        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', '投稿を更新しました。');
    }

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を削除する権限がありません。');

        $post->delete();

        return redirect()->route('posts.index')->with('success', '投稿を削除しました。');
    }
}
```

### Fat Controllerの問題点

1. **Eloquentクエリが直書きされている** — `index()` の中でクエリの詳細（`with`, `latest`, `paginate`）を直接書いており、Controllerがデータアクセスの実装詳細を知りすぎている。
2. **認可ロジックが重複している** — `edit`, `update`, `destroy` の3箇所に全く同じ `abort_if` が繰り返されている。ルールを変更する際に3箇所すべてを修正する必要がある。
3. **ビジネスロジックとHTTP処理が混在している** — `store()` で `user_id` をセットする処理と、リクエストを受けてレスポンスを返す処理が同じメソッド内に同居している。
4. **テストしにくい** — ロジックがControllerに直書きされているため、単体テストするにはHTTPリクエストを経由する必要がある。

## After（リファクタリング後）

### `app/Repositories/PostRepository.php`
Eloquentクエリをここに集約。Controllerは「どうやってデータを取るか」を知らなくてよい。

### `app/Services/PostService.php`
投稿の作成・更新・削除など、ビジネスロジック（`user_id` の付与など）をここに集約。

### `app/Policies/PostPolicy.php`
`abort_if` の重複を解消。`update`/`delete` の認可ルールを1箇所に集約し、`$this->authorize()` から呼び出す。

### `app/Http/Controllers/PostController.php`
Repository/Serviceを呼び出すだけの薄いControllerになった。

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}

    public function index()
    {
        $posts = $this->postService->getPosts();
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $this->postService->createPost($validated, auth()->id());

        return redirect()->route('posts.index')->with('success', '投稿を作成しました。');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    // update / destroy も同様に $this->authorize() 経由
}
```

## Before/After 比較まとめ

| 観点 | Before | After |
|---|---|---|
| Eloquentクエリ | Controllerに直書き | `PostRepository` に集約 |
| 認可ロジック | `abort_if` が3箇所に重複 | `PostPolicy` に1箇所へ集約 |
| ビジネスロジック | Controllerに混在 | `PostService` に分離 |
| Controllerの役割 | データ取得・加工・認可・レスポンスを全部担当 | リクエストを受けてServiceに渡すだけ |
| テストのしやすさ | HTTP経由でしかテストできない | Repository/Service/Policyを個別に単体テスト可能（設計上） |
| 認可ルール変更時の修正範囲 | 3メソッドすべてを修正 | Policy 1箇所を修正するだけ |

## 学び

Fat Controllerは機能面では正しく動作するが、「同じロジックの重複」と「責務の混在」により、変更に弱く壊れやすい設計になる。Repository/Service/Policyへの分離によって、Controller自体は「リクエストを受けて委譲するだけ」の薄い層になり、各層を独立してテスト・修正できるようになった。
