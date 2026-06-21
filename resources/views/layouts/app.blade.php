<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ブログ')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; line-height: 1.7; color: #333; background: #f5f5f5; }
        header { background: #2c3e50; color: #fff; padding: 1rem 0; }
        header .container { display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; text-decoration: none; }
        header h1 { font-size: 1.3rem; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 1rem; }
        main { padding: 2rem 0; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #3498db; color: #fff;
               text-decoration: none; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; }
        .alert { padding: 1rem; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 1rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .category-tag { display: inline-block; padding: 0.2rem 0.6rem; background: #ecf0f1;
                        border-radius: 12px; font-size: 0.85rem; color: #7f8c8d; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.3rem; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .error { color: #e74c3c; font-size: 0.85rem; margin-top: 0.3rem; }
        .pagination { display: flex; gap: 0.3rem; list-style: none; margin-top: 1.5rem; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 0.4rem 0.8rem; background: #fff; border-radius: 4px; text-decoration: none; color: #3498db; }
        .pagination .active span { background: #3498db; color: #fff; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="{{ route('posts.index') }}">📝 My Blog</a></h1>
            <a href="{{ route('posts.create') }}" class="btn">新規投稿</a>
        </div>
    </header>
    <main>
        <div class="container">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
