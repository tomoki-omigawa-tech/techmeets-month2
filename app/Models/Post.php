<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use HasFactory;

    // 一覧キャッシュのバージョン番号を保存するキー
    public const INDEX_CACHE_VERSION_KEY = 'posts.index.version';

    protected $fillable = ['title', 'body', 'user_id', 'category_id'];

    protected static function booted(): void
    {
        // 作成・更新・削除のたびに一覧キャッシュを無効化する
        static::saved(fn () => static::flushIndexCache());
        static::deleted(fn () => static::flushIndexCache());
    }

    // バージョン番号を上げて、古い一覧キャッシュを参照されないようにする
    // （Cache::tags() はRedis専用のため、どのキャッシュドライバーでも動くこの方式を採用）
    public static function flushIndexCache(): void
    {
        Cache::add(self::INDEX_CACHE_VERSION_KEY, 1);
        Cache::increment(self::INDEX_CACHE_VERSION_KEY);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // いいね数を1増やす
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
        static::flushIndexCache();
    }
}
