<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    // 編集権限：投稿者本人のみ
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // 削除権限：投稿者本人のみ
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
