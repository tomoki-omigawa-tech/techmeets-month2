<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class PostRepository
{
    // 一覧取得（カテゴリ・投稿者込み・ページネーション・10分キャッシュ）
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        $page = request()->integer('page', 1);
        $version = Cache::get(Post::INDEX_CACHE_VERSION_KEY, 1);
        $key = "posts.index.v{$version}.page{$page}.per{$perPage}";

        return Cache::remember($key, now()->addMinutes(10), function () use ($perPage) {
            return Post::with(['category', 'user:id,name'])->latest()->paginate($perPage);
        });
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
