<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // テストユーザーを作成（既に存在する場合は取得）
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
            ]
        );

        // カテゴリーを3つ作成
        $tech = Category::firstOrCreate(['name' => '技術']);
        $diary = Category::firstOrCreate(['name' => '日記']);
        $news = Category::firstOrCreate(['name' => 'お知らせ']);

        // サンプル投稿を作成
        Post::create([
            'user_id' => $user->id,
            'title' => 'Laravelを始めました',
            'body' => 'MVCパターンでブログシステムを作っています。まずは投稿一覧から実装中です。',
            'category_id' => $tech->id,
        ]);

        Post::create([
            'user_id' => $user->id,
            'title' => 'マイグレーションとモデルのリレーションを学びました',
            'body' => 'マイグレーションとモデルのリレーションを学びました。Eloquentは便利ですね。',
            'category_id' => $diary->id,
        ]);

        Post::create([
            'user_id' => $user->id,
            'title' => 'ブログを公開しました',
            'body' => 'このブログシステムはWeek7の課題として作成しています。',
            'category_id' => $news->id,
        ]);

        // ページネーション確認用にダミー投稿を15件追加
        for ($i = 1; $i <= 15; $i++) {
            Post::create([
                'user_id' => $user->id,
                'title' => "サンプル投稿 {$i}",
                'body' => "これはページネーション動作確認用のサンプル投稿{$i}です。",
                'category_id' => $tech->id,
            ]);
        }
    }
}
