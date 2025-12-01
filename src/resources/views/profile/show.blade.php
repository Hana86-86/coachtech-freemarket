@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="container profile-page">

    {{-- ユーザー情報 --}}
    <header class="mp-head">
        <img src="{{ $user->avatarUrl }}" alt="{{ $user->name }}" class="mp-avatar">
        <div class="mp-user">
            <h1 class="mp-user-name">{{ $user->name }}</h1>
            <a href="{{ route('profile.edit') }}" class="btn btn--ghost btn--sm profile-edit">プロフィールを編集</a>
        </div>
    </header>

    {{-- タブ --}}
    <nav class="products_tabs" aria-label="マイページのタブ">
        <a href="{{ route('profile.show', ['tab' => 'selling']) }}"
            class="mypage-tab {{ $currentTab === 'selling' ? 'is-active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('profile.show', ['tab' => 'bought']) }}"
            class="mypage-tab {{ $currentTab === 'bought' ? 'is-active' : '' }}">
            購入した商品
        </a>
        <a href="{{ route('profile.show', ['tab' => 'trading']) }}"
            class="mypage-tab {{ $currentTab === 'trading' ? 'is-active' : '' }}">
            取引中の商品
        </a>
    </nav>
    {{-- タブ下の区切り線 --}}
    <hr class="mp-divider">

    <div class="mp-cards">

        {{-- ★ コンテンツ切り替え --}}
        @if ($currentTab === 'selling')
        {{-- 出品した商品タブ --}}
        @forelse ($myProducts as $product)
        <article class="gp-cards">
            {{-- 商品詳細へのリンク --}}
            <a href="{{ route('products.show', $product->id) }}" class="gp-thumb">
                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_thumb">
            </a>

            <div class="card_body">
                <h3 class="card_title">{{ $product->title }}</h3>
                <p class="card_price">¥{{ number_format($product->price) }}</p>
            </div>
        </article>
        @empty
        <p class="muted">出品した商品はありません。</p>
        @endforelse

        @elseif ($currentTab === 'bought')
        {{-- 購入した商品（取引完了）タブ --}}
        @forelse ($purchases as $purchase)
        @php $product = $purchase->product; @endphp

        <article class="gp-cards">
            <a href="{{ route('products.show', $product->id) }}" class="gp-thumb">
                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_thumb">
            </a>

            <div class="card_body">
                <h3 class="card_title">{{ $product->title }}</h3>
                <p class="card_price">¥{{ number_format($purchase->amount) }}</p>
            </div>
        </article>
        @empty
        <p class="muted">購入した商品はありません。</p>
        @endforelse

        @elseif ($currentTab === 'trading')
        {{-- 取引中の商品タブ --}}
        @forelse ($tradingPurchases as $purchase)
        @php $product = $purchase->product; @endphp

        <article class="gp-cards">
            {{-- 取引チャット画面へのリンクにしてもOK（後で Step3 で活用） --}}
            <a href="{{ route('trades.chat.show', $purchase) }}" class="gp-thumb">
                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_thumb">
            </a>

            <div class="card_body">
                <h3 class="card_title">{{ $product->title }}</h3>
                <p class="card_price">¥{{ number_format($purchase->amount) }}</p>
                <p class="badge badge--status">取引中</p>
            </div>
        </article>
        @empty
        <p class="muted">取引中の商品はありません。</p>
        @endforelse
        @endif
    </div>
    @endsection