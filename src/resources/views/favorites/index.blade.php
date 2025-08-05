@extends('layouts,app')

@section('title','マイリスト')

@section('content')
<h2>お気に入り</h2>
<div class="item-list">
    @forelse($favorites as $favorite)
    <div class="item_card">
        <img src="{{ $favorite->product->image_path }}" alt="{{ $favorite->product->title }}">
        <h3>{{ $favorite->product->title }}</h3>
        <p>¥{{ number_format($favorite->product->price) }}</p>
        <form action="{{ route('favorites.destroy', $favorite->product->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">お気に入り解除</button>
        </form>
    </div>
    @empty
        <p>お気に入りに登録した商品はありません。</p>
    @endforelse
</div>
@endsection