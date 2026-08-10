<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 投稿一覧：誰でも閲覧可能
Route::get('/posts', [PostController::class, 'index']);

// 投稿作成：ログインユーザーのみ
Route::middleware('auth:sanctum')->post('/posts', [PostController::class, 'store']);
