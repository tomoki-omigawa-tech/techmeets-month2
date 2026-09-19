<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_posts_returns_paginated_result_from_repository()
    {
        // 準備
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $repository = Mockery::mock(PostRepository::class);
        $repository->shouldReceive('paginate')
            ->once()
            ->andReturn($paginator);

        $service = new PostService($repository);

        // 実行
        $result = $service->getPosts();

        // 検証
        $this->assertSame($paginator, $result);
    }

    public function test_create_post_sets_user_id_and_calls_repository()
    {
        // 準備
        $validated = ['title' => 'Test Title', 'body' => 'Test Body'];
        $userId = 5;
        $expectedPost = Mockery::mock(Post::class);

        $repository = Mockery::mock(PostRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($userId) {
                return $data['user_id'] === $userId
                    && $data['title'] === 'Test Title';
            }))
            ->andReturn($expectedPost);

        $service = new PostService($repository);

        // 実行
        $result = $service->createPost($validated, $userId);

        // 検証
        $this->assertSame($expectedPost, $result);
    }

    public function test_create_post_with_empty_validated_array_still_sets_user_id()
    {
        // 準備（境界値：空配列でもuser_idだけは必ずセットされるか）
        $validated = [];
        $userId = 1;
        $expectedPost = Mockery::mock(Post::class);

        $repository = Mockery::mock(PostRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->with(['user_id' => $userId])
            ->andReturn($expectedPost);

        $service = new PostService($repository);

        // 実行
        $result = $service->createPost($validated, $userId);

        // 検証
        $this->assertSame($expectedPost, $result);
    }

    public function test_update_post_calls_repository_and_returns_updated_post()
    {
        // 準備
        $post = Mockery::mock(Post::class);
        $validated = ['title' => 'Updated Title'];
        $updatedPost = Mockery::mock(Post::class);

        $repository = Mockery::mock(PostRepository::class);
        $repository->shouldReceive('update')
            ->once()
            ->with($post, $validated)
            ->andReturn($updatedPost);

        $service = new PostService($repository);

        // 実行
        $result = $service->updatePost($post, $validated);

        // 検証
        $this->assertSame($updatedPost, $result);
    }

    public function test_delete_post_calls_repository_delete()
    {
        // 準備
        $post = Mockery::mock(Post::class);

        $repository = Mockery::mock(PostRepository::class);
        $repository->shouldReceive('delete')
            ->once()
            ->with($post);

        $service = new PostService($repository);

        // 実行
        $service->deletePost($post);

        // 検証（deleteが呼ばれたことはshouldReceiveのonce()で保証される）
        $this->assertTrue(true);
    }
}
