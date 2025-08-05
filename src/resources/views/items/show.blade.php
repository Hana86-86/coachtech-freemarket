@extends('layouts.app')

@section('title', '商品詳細')

@section('content')
<div class="item-detail">
    <h2>{{ $product->title }}</h2>
    <img src="{{ $product->image_path }}" alt="{{ $product->title }}" class="item-detail__image">

    <p class="item-detail__price">価格: ¥{{ number_format($product->price) }}</p>
    <p class="item-detail__condition">状態: {{ $product->condition }}</p>
    <p class="item-detail__description">{{ $product->description }}</p>

    <a href="{{ route('items.index') }}" class="btn">一覧に戻る</a>
</div>
@endsection