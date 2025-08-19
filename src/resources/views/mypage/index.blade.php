@extends('layouts.app')

@section('title','マイページ')

@section('content')
<div class="container">
    <h1 class="page-title">マイページ</h1>

    <div class="panel" style="margin-bottom:16px;">
        <strong>{{ $user->name }}</strong><br>
        <small>{{ $user->email }}</small>
    </div>

    <div class="tabs">
        <a href="#my" class="tab is-active">出品一覧</a>
        <a href="#comments" class="tab">コメント</a>
        <a href="#favorites" class="tab">お気に入り</a>
        <a href="#buy" class="tab">購入履歴</a>
        <a href="#likes" class="tab">いいね</a>
    </div>

    {{-- 出品商品 --}}
    <section id="my" class="panel mt-20">
        <h2 class="section-title">出品した商品</h2>
        @if ($myProducts->count())
            <div class="cards">
                @foreach ($myProducts as $product)
                <article class="card">
                    @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}">
                    @endif
                    <h3 class="card-title">{{ $product->title }}</h3>
                    <p class="card-price">¥{{ number_format($product->price) }}</p>
                    <a class="link-text" href="{{ route('products.show', $product) }}">詳細</a>
                </article>
                @endforeach
            </div>
            {{ $myProducts->links() }}
        @else
            <p>出品商品はありません。</p>
        @endif
    </section>

    {{-- コメント --}}
    <section id="comments" class="panel mt-20">
        <h2 class="section-title">コメント</h2>
        @if ($comments->count())
        <div class="cards">
            @foreach ($comments as $comment)
            <article class="card">
                <div class="card-body">
                    <p class="card-text">{{ $comment->body }}</p>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small><br>
                    @if($comment->product)
                    <a class="link-text" href="{{ route('products.show', $comment->product) }}">
                        {{ $comment->product->title }}へ
                    </a>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        {{ $comments->links() }}
        @else
            <p>コメントはありません。</p>
        @endif
    </section>

    {{-- お気に入り --}}
    <section id="favorites" class="panel mt-20">
        <h2 class="section-title">お気に入り</h2>
        @if ($favorites->count())
        <div class="cards">
            @foreach ($favorites as $favorite)
            @php $product = $favorite->product; @endphp
            @if($product)
            <article class="card">
                @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}">
                @endif
                <h3 class="card-title">{{ $product->title }}</h3>
                <p class="card-price">¥{{ number_format($product->price) }}</p>
                <a class="link-text" href="{{ route('products.show', $product) }}">詳細</a>
            </article>
            @endif
            @endforeach
        </div>
        @else
            <p>お気に入りはありません。</p>
        @endif
    </section>

    {{-- 購入履歴 --}}
    <section id="buy" class="panel mt-20">
        <h2 class="section-title">購入履歴</h2>
        @if ($purchases->count())
        <ul class="list">
            @foreach ($purchases as $purchase)
            @php $product = $purchase->product ;@endphp
            @if($product)
            <li class="list__item">
                <a href="{{ route('products.show', $product) }}">{{ $product->title }}</a>
                <img src="{{ asset($purchase->product->image_path) }}" alt="{{ $purchase->product->title }}">
                <span> / ¥{{ number_format($product->price) }}</span>
                <span> / {{ $purchase->created_at->format('Y-m-d') }}</span>
                </li>
                @endif
            @endforeach
        </ul>
        {{ $purchases->links() }}
        @else
            <p>購入履歴はまだありません。</p>
        @endif
    </section>

    {{-- いいね --}}
    <section id="likes" class="panel mt-20">
        <h2 class="section-title">いいね</h2>
        @if ($likeProducts->count())
        <div class="cards">
            @foreach ($likeProducts as $product)
            <article class="card">
                @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}">
                @endif
                <h3 class="card-title">{{ $product->title }}</h3>
                <p class="card-price">¥{{ number_format($product->price) }}</p>
                <a class="link-text" href="{{ route('products.show', $product) }}">詳細</a>
            </article>
            @endforeach
        </div>
        {{ $likeProducts->links() }}
        @else
            <p>いいねはありません。</p>
        @endif
    </section>
</div>
@endsection