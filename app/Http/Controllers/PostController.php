<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 一覧表示（ページネーション付き）
    public function index()
    {
        $posts = Post::with('category')->latest()->paginate(10);
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

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect()->route('posts.index')
            ->with('success', '投稿を作成しました。');
    }

    // 編集フォーム表示
    public function edit(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を編集する権限がありません。');

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    // 更新処理
    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を編集する権限がありません。');

        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', '投稿を更新しました。');
    }

    // 削除処理
    public function destroy(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403, 'この投稿を削除する権限がありません。');

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', '投稿を削除しました。');
    }
}
