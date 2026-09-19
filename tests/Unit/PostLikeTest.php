<?php

namespace Tests\Unit;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_post_starts_with_zero_likes()
    {
        // 準備・実行
        $post = Post::factory()->create();

        // 検証
        $this->assertSame(0, $post->likes_count);
    }

    public function test_incrementing_likes_increases_count_by_one()
    {
        // 準備
        $post = Post::factory()->create(['likes_count' => 0]);

        // 実行
        $post->incrementLikes();

        // 検証
        $this->assertSame(1, $post->fresh()->likes_count);
    }
}
