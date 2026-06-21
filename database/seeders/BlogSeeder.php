<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // カテゴリーを3つ作成
        $tech = Category::create(['name' => '技術']);
        $diary = Category::create(['name' => '日記']);
        $news = Category::create(['name' => 'お知らせ']);

        // サンプル投稿を作成
        Post::create([
            'title' => 'Laravelを始めました',
            'body' => 'MVCパターンでブログシステムを作っています。まずは投稿一覧から実装中です。',
            'category_id' => $tech->id,
        ]);

        Post::create([
            'title' => '今日の学習記録',
            'body' => 'マイグレーションとモデルのリレーションを学びました。Eloquentは便利ですね。',
            'category_id' => $diary->id,
        ]);

        Post::create([
            'title' => 'ブログを公開しました',
            'body' => 'このブログシステムはWeek7の課題として作成しています。',
            'category_id' => $news->id,
        ]);

        // ページネーション確認用にダミー投稿を15件追加
        for ($i = 1; $i <= 15; $i++) {
            Post::create([
                'title' => "サンプル投稿 {$i}",
                'body' => "これはページネーション動作確認用のサンプル投稿{$i}です。",
                'category_id' => $tech->id,
            ]);
        }
    }
}
