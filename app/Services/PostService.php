<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        protected PostRepository $postRepository
    ) {
    }

    // 一覧取得
    public function getPosts(): LengthAwarePaginator
    {
        return $this->postRepository->paginate();
    }

    // 投稿作成（作成者をセットする責務はここに置く）
    public function createPost(array $validated, int $userId): Post
    {
        $validated['user_id'] = $userId;
        return $this->postRepository->create($validated);
    }

    // 投稿更新
    public function updatePost(Post $post, array $validated): Post
    {
        return $this->postRepository->update($post, $validated);
    }

    // 投稿削除
    public function deletePost(Post $post): void
    {
        $this->postRepository->delete($post);
    }
}
