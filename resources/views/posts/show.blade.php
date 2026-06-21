@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="card">
        <span class="category-tag">{{ $post->category->name }}</span>
        <h2>{{ $post->title }}</h2>
        <small>{{ $post->created_at->format('Y年m月d日 H:i') }}</small>
        <hr style="margin: 1rem 0;">
        <p style="white-space: pre-wrap;">{{ $post->body }}</p>
    </div>

    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">一覧へ戻る</a>
        <a href="{{ route('posts.edit', $post) }}" class="btn">編集</a>
        <form action="{{ route('posts.destroy', $post) }}" method="POST"
              onsubmit="return confirm('本当に削除しますか？');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    </div>
@endsection
