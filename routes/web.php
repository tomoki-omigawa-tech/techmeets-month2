<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3UploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 投稿：作成・編集・削除はログインユーザーのみ（先に登録することで /posts/create が /posts/{post} より優先される）
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->except(['index', 'show']);
});

// 投稿：一覧・詳細は誰でも閲覧可能
Route::resource('posts', PostController::class)->only(['index', 'show']);

require __DIR__.'/auth.php';

Route::get('/s3upload', [S3UploadController::class, 'index'])->name('s3upload.index');
Route::post('/s3upload', [S3UploadController::class, 'store'])->name('s3upload.store');
