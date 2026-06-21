@extends('layouts.app')

@section('title', '新規投稿')

@section('content')
    <h2>新規投稿</h2>

    <div class="card">
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">タイトル</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}">
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">カテゴリー</label>
                <select name="category_id" id="category_id">
                    <option value="">選択してください</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                <textarea name="body" id="body" rows="8">{{ old('body') }}</textarea>
                @error('body')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">キャンセル</a>
                <button type="submit" class="btn">投稿する</button>
            </div>
        </form>
    </div>
@endsection
