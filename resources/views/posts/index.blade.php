@extends('layouts.app')

@section('title', '投稿一覧')

@section('content')
    <h2>投稿一覧</h2>

    @forelse ($posts as $post)
        <div class="card">
            <span class="category-tag">{{ $post->category->name }}</span>
            <h3>
                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
            </h3>
            <p>{{ Str::limit($post->body, 80) }}</p>
            <small>{{ $post->created_at->format('Y年m月d日') }}</small>
        </div>
    @empty
        <p>投稿がまだありません。</p>
    @endforelse

    {{ $posts->links() }}
@endsection
