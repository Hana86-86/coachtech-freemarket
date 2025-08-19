@extends('layouts.app')

@section('content')
<div class="product-list" style="max-width:1000px; margin:20px auto;">
    @forelse($purchases as $p)
        <div class="product-card">
            <img src="{{ $p->product->image_path }}" alt="{{ $p->product->title }}">
            <h2>{{ $p->product->title }}</h2>
            <div class="price">¥{{ number_format($p->amount) }}</div>
            <p class="condition">支払い方法: {{ $p->payment_method === 'konbini' ? 'コンビニ払い' : 'カード' }}</p>
            <p class="condition">購入日: {{ optional($p->paid_at)->format('Y-m-d H:i') }}</p>
            <a href="{{ route('products.show', $p->product->id) }}">商品ページへ</a>
        </div>
        @empty
        <p>購入履歴はまだありません。</p>
        @endforelse
</div>
@endsection