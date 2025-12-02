{{-- resources/views/trades/show.blade.php --}}
@extends('layouts.app')

@section('title', '取引チャット')

@section('content')
<h1 class="text-xl font-semibold mb-4">
    「{{ $purchase->product->title }}」の取引画面
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- 左カラム：商品情報 --}}
    <section class="md:col-span-1 bg-white border rounded-lg p-4 shadow-sm">
        <h2 class="text-sm font-semibold mb-3">商品情報</h2>

        {{-- 商品画像（ダミー） --}}
        <div class="mb-3">
            <img
                src="{{ $purchase->product->image_url }}"
                alt="{{ $purchase->product->title }}"
                class="w-full object-cover rounded">
        </div>

        <p class="text-sm font-semibold">
            {{ $purchase->product->title }}
        </p>
        <p class="text-sm text-slate-600">
            ¥{{ number_format($purchase->product->price) }}
        </p>
    </section>

    {{-- 右カラム：チャットエリアの雛形 --}}
    <section class="md:col-span-2 bg-white border rounded-lg p-4 shadow-sm flex flex-col">
        <h2 class="text-sm font-semibold mb-3">取引メッセージ</h2>

        <a href="{{ route('trades.chat', $purchase) }}"
            class="px-4 py-2 bg-sky-500 text-white rounded">
            チャットへ進む
        </a>

        {{-- ここにメッセージ一覧が入る予定 --}}
        <div class="flex-1 border rounded mb-3 p-3 text-xs text-slate-500">
            ※ このエリアに取引メッセージを表示します（まだ未実装）
        </div>

        {{-- 入力フォームの雛形 --}}
        <form action="#" method="post" class="flex gap-2">
            @csrf
            <input
                type="text"
                name="content"
                class="flex-1 border rounded px-3 py-2 text-sm"
                placeholder="取引メッセージを入力してください"
                disabled {{-- まだ実装前なので入力不可にしておく --}}>
            <button
                type="submit"
                class="px-4 py-2 text-xs rounded bg-slate-300 text-slate-700 cursor-not-allowed"
                disabled>
                送信（未実装）
            </button>
        </form>
    </section>
</div>
@endsection