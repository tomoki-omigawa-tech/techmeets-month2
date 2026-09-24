<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\S3UploadController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

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
Route::get('/s3upload', [S3UploadController::class, 'index'])->middleware('auth')->name('s3upload.index');
Route::post('/s3upload', [S3UploadController::class, 'store'])->middleware('auth')->name('s3upload.store');

// Stripe決済
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Stripe Webhook
Route::post('/api/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('webhook.stripe');
