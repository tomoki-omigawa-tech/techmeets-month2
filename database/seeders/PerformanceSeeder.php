<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()->count(50)->create();
        $categories = Category::factory()->count(10)->create();

        Post::factory()
            ->count(3000)
            ->recycle($users)
            ->recycle($categories)
            ->state(fn () => [
                'likes_count' => fake()->numberBetween(0, 500),
                'created_at' => fake()->dateTimeBetween('-1 year'),
            ])
            ->create();
    }
}
