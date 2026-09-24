<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class S3UploadController extends Controller
{
    public function index()
    {
        return view('s3upload.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $file = $request->file('image');

        // 横幅1600pxを上限に縮小し、WebP（品質80）に変換
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        $image->scaleDown(width: 1600);
        $webp = (string) $image->toWebp(quality: 80);

        // S3に保存（ファイル名はUUIDで衝突を防ぐ。CloudFront・ブラウザで長期キャッシュさせる）
        $path = 'images/' . Str::uuid() . '.webp';
        Storage::disk('s3')->put($path, $webp, [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        $originalKb = round($file->getSize() / 1024, 1);
        $webpKb = round(strlen($webp) / 1024, 1);
        $reduction = $originalKb > 0 ? round((1 - $webpKb / $originalKb) * 100, 1) : 0;

        Log::info('WebP conversion', [
            'original_name' => $file->getClientOriginalName(),
            'original_kb' => $originalKb,
            'webp_kb' => $webpKb,
            'reduction_percent' => $reduction,
            'width' => $image->width(),
            'height' => $image->height(),
            'path' => $path,
        ]);

        return redirect()->route('s3upload.index')
            ->with('url', Storage::disk('s3')->url($path))
            ->with('conversion', "元画像 {$originalKb} KB → WebP {$webpKb} KB（{$reduction}% 削減）");
    }
}
