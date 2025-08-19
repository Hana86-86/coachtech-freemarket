@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="product-list">
    @forelse($products as $product)
        <div class="product-card">
            <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}">

            @if($product->is_sold)
            <span class="sold-label">SOLD</span>
            @endif

            <h2>{{ $product->title }}</h2>
            <p class="price">¥{{ number_format($product->price) }}</p>
            <p class="condition">状態: {{ $product->condition }}</p>

            <a href="{{ route('products.show', $product->id) }}">詳細を見る</a>
        </div>
        @empty
        <p>商品が見つかりませんでした。</p>
    @endforelse
</div>
@endsection