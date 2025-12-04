@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="container profile-page">

    {{-- ユーザー情報 --}}
    <header class="mp-head">
        <img src="{{ $user->avatarUrl }}" alt="{{ $user->name }}" class="mp-avatar">

        <div class="mp-user">
            <h1 class="mp-user-name">{{ $user->name }}</h1>
            <div class="mp-user-rating">
                {{-- 出品購入どちらも評価がない場合 --}}
                @if (is_null($buyerRatingAvg) && is_null($sellerRatingAvg))
                <span class="mp-user-rating__label">まだ評価はありません</span>
                @else
                {{-- 購入者として評価ありの場合 --}}
                @if (!is_null($buyerRatingAvg))
                <div class="mp-user-rating__row">
                    <span class="mp-user-rating__label">購入者としての評価</span>

                    @php
                    $buyerStarScore = (int) round($buyerRatingAvg);
                    @endphp

                    <span class="map-user-rating__stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="mp-user-rating__star">
                            {{ $i <= $buyerStarScore ? '★' : '☆'}}
                    </span>
                    @endfor
                    </span>

                    <span class="mp-user-rating__value">
                        ({{ $buyerRatingAvg }} / 5)
                    </span>
                </div>
                @endif
                {{-- 出品者として評価ありの場合 --}}
                @if (!is_null($sellerRatingAvg))
                <div class="mp-user-rating__row">
                    <span class="mp-user-rating__label">出品者としての評価</span>

                    @php
                    $sellerStarScore = (int) round($sellerRatingAvg);
                    @endphp

                    <span class="map-user-rating__stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="mp-user-rating__star">
                            {{ $i <= $sellerStarScore ? '★' : '☆'}}
                    </span>
                    @endfor
                    </span>

                    <span class="mp-user-rating__value">
                        ({{ $sellerRatingAvg }} / 5)
                    </span>
                </div>
                @endif
                @endif
            </div>
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
        {{-- ★ 取引中タブ：未読があればクラス has-unread を付ける --}}
        @php
        $hasUnread = !empty($tradingUnreadTotal) && $tradingUnreadTotal > 0;
        @endphp
        <a href="{{ route('profile.show', ['tab' => 'trading']) }}"
            class="mypage-tab mypage-tab--trading {{ $currentTab === 'trading' ? 'is-active' : '' }} {{ $hasUnread ? 'has-unread' : '' }}">
            取引中の商品

            {{-- ★ 未読が1件以上あるときだけ赤丸＋件数を表示 --}}
            @if ($hasUnread)
            <span class="mypage-tab__badge">
                {{ $tradingUnreadTotal }}
            </span>
            @endif
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
        <div class="product-grid">
            @forelse ($tradingPurchases as $purchase)
            @php
            $product = $purchase->product;
            @endphp

            <div class="product-card">
                {{-- ★ バッジ（未読件数） --}}
                @if (!empty($purchase->unread_count) && $purchase->unread_count > 0)
                <div class="product-card__badge">
                    {{ $purchase->unread_count }}
                </div>
                @endif

                <a href="{{ route('trades.chat.show', $purchase) }}" class="gp-thumb">
                    <div class="product-card__image">
                        <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_thumb">
                    </div>
                    <div class="product-card__body">
                        <div class="product-card__title">{{ $product->title }}</div>
                        <div class="product-card__price">¥{{ number_format($purchase->amount) }}</div>
                    </div>
                </a>
            </div>
            @empty
            <p>取引中の商品はありません。</p>
            @endforelse
        </div>
        @endif
    </div>
    @endsection