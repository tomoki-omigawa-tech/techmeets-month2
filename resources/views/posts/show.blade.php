<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $post->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <span class="text-xs text-gray-500">{{ $post->category->name }}</span>
                <h1 class="text-2xl font-bold mt-2">{{ $post->title }}</h1>
                <p class="text-sm text-gray-400 mb-4">
                    投稿者: {{ $post->user->name }} ／ {{ $post->created_at->format('Y年m月d日') }}
                </p>
                <div class="prose max-w-none">
                    {!! nl2br(e($post->body)) !!}
                </div>

                @auth
                    @if ($post->user_id === auth()->id())
                        <div class="mt-6 flex gap-2">
                            <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">編集</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('本当に削除しますか?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">削除</button>
                            </form>
                        </div>
                    @endif
                @endauth

                <div class="mt-6">
                    <a href="{{ route('posts.index') }}" class="text-indigo-600 hover:underline">← 一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
