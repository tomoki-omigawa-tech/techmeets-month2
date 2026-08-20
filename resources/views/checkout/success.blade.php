<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済完了
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-lg text-green-600 font-bold">お支払いが完了しました。</p>
                <p class="mt-2 text-gray-600">ご購入ありがとうございます。</p>
                <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-blue-600 underline">ダッシュボードへ戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>
