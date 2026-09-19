<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_post_index()
    {
        // 準備
        Post::factory()->count(3)->create();

        // 実行
        $response = $this->get('/posts');

        // 検証
        $response->assertStatus(200);
    }

    public function test_guest_can_view_post_show()
    {
        // 準備
        $post = Post::factory()->create();

        // 実行
        $response = $this->get("/posts/{$post->id}");

        // 検証
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_create_page()
    {
        // 実行
        $response = $this->get('/posts/create');

        // 検証
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_post()
    {
        // 準備
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // 実行
        $response = $this->actingAs($user)->post('/posts', [
            'title' => 'Test Title',
            'body' => 'Test Body',
            'category_id' => $category->id,
        ]);

        // 検証
        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Title',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_post_validation_fails_with_empty_title()
    {
        // 準備
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // 実行
        $response = $this->actingAs($user)->post('/posts', [
            'title' => '',
            'body' => 'Test Body',
            'category_id' => $category->id,
        ]);

        // 検証
        $response->assertSessionHasErrors(['title']);
    }

    public function test_owner_can_update_own_post()
    {
        // 準備
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 実行
        $response = $this->actingAs($user)->put("/posts/{$post->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'category_id' => $category->id,
        ]);

        // 検証
        $response->assertRedirect("/posts/{$post->id}");
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_non_owner_cannot_update_others_post()
    {
        // 準備
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // 実行
        $response = $this->actingAs($otherUser)->put("/posts/{$post->id}", [
            'title' => 'Hacked Title',
            'body' => 'Hacked Body',
            'category_id' => $category->id,
        ]);

        // 検証
        $response->assertStatus(403);
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
            'title' => 'Hacked Title',
        ]);
    }

    public function test_owner_can_delete_own_post()
    {
        // 準備
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 実行
        $response = $this->actingAs($user)->delete("/posts/{$post->id}");

        // 検証
        $response->assertRedirect('/posts');
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_non_owner_cannot_delete_others_post()
    {
        // 準備
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // 実行
        $response = $this->actingAs($otherUser)->delete("/posts/{$post->id}");

        // 検証
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }
}
