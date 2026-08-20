<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済キャンセル
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-lg text-red-600 font-bold">決済がキャンセルされました。</p>
                <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-blue-600 underline">ダッシュボードへ戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>
