@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="item-list">
    @forelse($products as $product)
        <div class="item-card">
            <img src="{{ $product->image_path }}" alt="{{ $product->title }}">
            <h2>{{ $product->title }}</h2>
            <p class="price">¥{{ number_format($product->price) }}</p>
            <p class="condition">状態: {{ $product->condition }}</p>
            <a href="{{ route('items.show', $product->id) }}">詳細を見る</a>
        </div>
        @empty
        <p>商品が見つかりませんでした。</p>
    @endforelse
</div>
@endsection