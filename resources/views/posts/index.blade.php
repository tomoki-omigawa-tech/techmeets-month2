<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">投稿一覧</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                <div class="mb-4 text-right">
                    <a href="{{ route('posts.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        新規投稿
                    </a>
                </div>
            @endauth

            @forelse ($posts as $post)
                <div class="card bg-white p-4 mb-4 rounded shadow">
                    <span class="category-tag text-xs text-gray-500">{{ $post->category->name }}</span>
                    <h3 class="text-lg font-semibold">
                        <a href="{{ route('posts.show', $post) }}" class="text-indigo-600 hover:underline">{{ $post->title }}</a>
                    </h3>
                    <p class="text-gray-700">{{ Str::limit($post->body, 80) }}</p>
                    <small class="text-gray-400">{{ $post->created_at->format('Y年m月d日') }}</small>
                </div>
            @empty
                <p>投稿がまだありません。</p>
            @endforelse

            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
