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
    ) {
    }

    // 一覧表示（ページネーション付き）
    public function index()
    {
        $posts = $this->postService->getPosts();
        return view('posts.index', compact('posts'));
    }

    // 詳細表示
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    // 作成フォーム表示
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    // 作成処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $this->postService->createPost($validated, auth()->id());

        return redirect()->route('posts.index')
            ->with('success', '投稿を作成しました。');
    }

    // 編集フォーム表示
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    // 更新処理
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $this->postService->updatePost($post, $validated);

        return redirect()->route('posts.show', $post)
            ->with('success', '投稿を更新しました。');
    }

    // 削除処理
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->postService->deletePost($post);

        return redirect()->route('posts.index')
            ->with('success', '投稿を削除しました。');
    }
}
