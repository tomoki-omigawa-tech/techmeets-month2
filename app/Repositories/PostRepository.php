<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository
{
    // 一覧取得（カテゴリ込み・ページネーション）
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Post::with(['category', 'user:id,name'])->latest()->paginate($perPage);
    }

    // 新規作成
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    // 更新
    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    // 削除
    public function delete(Post $post): void
    {
        $post->delete();
    }
}
