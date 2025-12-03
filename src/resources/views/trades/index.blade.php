@extends('layouts.app')

@section('title', '取引中の商品')

@section('content')
<h1 class="text-xl font-semibold mb-6">取引中の商品</h1>

@if ($trades->isEmpty())
<p class="text-sm text-slate-600">
    現在進行中の取引はありません。
</p>
@else
<table class="w-full text-sm border-collapse">
    <thead>
        <tr class="border-b">
            <th class="py-2 text-left">商品</th>
            <th class="py-2 text-left">相手ユーザー</th>
            <th class="py-2 text-left">自分の役割</th>
            <th class="py-2 text-left">ステータス</th>
            <th class="py-2 text-left"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($trades as $trade)
        @php
        $isBuyer = $trade->buyer_id === $userId;
        $partner = $isBuyer ? $trade->product->user : $trade->buyer;
        @endphp

        <tr class="border-b">
            <td class="py-2">
                {{ $trade->product->title }}
            </td>
            <td class="py-2">
                {{ $partner->name }}
            </td>
            <td class="py-2">
                {{ $isBuyer ? '購入者' : '出品者' }}
            </td>
            <td class="py-2">
                {{ $trade->status }}
            </td>
            <td class="py-2">
                <a href="{{ route('trades.show', $trade) }}"
                    class="text-xs text-sky-600 hover:underline">
                    取引画面を開く
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection