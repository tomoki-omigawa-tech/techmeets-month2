@extends('layouts.app')

@section('title', '投稿編集')

@section('content')
    <h2>投稿編集</h2>

    <div class="card">
        <form action="{{ route('posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">タイトル</label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}">
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">カテゴリー</label>
                <select name="category_id" id="category_id">
                    <option value="">選択してください</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="body">内容</label>
                <textarea name="body" id="body" rows="8">{{ old('body', $post->body) }}</textarea>
                @error('body')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">キャンセル</a>
                <button type="submit" class="btn">更新する</button>
            </div>
        </form>
    </div>
@endsection
