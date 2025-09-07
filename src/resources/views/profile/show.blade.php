@extends('layouts.app')

@section('title', 'マイページ')

@php
    $tab = request('tab', 'my');
@endphp

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
    <a href="{{ route('profile.show', ['tab' => 'my']) }}" class="products_tab {{ $tab === 'my' ? 'is-active' : '' }}">
    出品した商品
</a>
    <a href="{{ route('profile.show', ['tab' => 'bought']) }}" class="products_tab {{ $tab === 'bought' ? 'is-active' : '' }}">
    購入した商品
</a>
</nav>
{{-- タブ下の区切り線 --}}
<hr class="mp-divider">

{{-- 出品した商品 --}}
@if($tab==='my')
<div class="mp-cards">

    @forelse($myProducts as $product)
        <article class="card">
            <a href="{{ route('products.show', $product) }}" class="mp-thumb">
            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_thumb">
            @if($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
            <span class="badge badge--sold">SOLD</span>
            @endif
        </a>
        <div class="card_body">
            <h3 class="card_title">{{ $product->title }}</h3>
            <p class="card_price">¥{{ number_format($product->price) }}</p>
        </div>
        </article>
        @empty
        <p class="muted">出品商品はありません。</p>
        @endforelse
    </div>
    @else
    <div class="mp-cards">
    @forelse($purchases as $purchase)
        @php $p = $purchase->product; @endphp
        <article class="card">
        <a href="{{ route('products.show', $p) }}" class="mp-thumb">
            <img src="{{ $p->image_url }}" alt="{{ $p->title }}" class="card_thumb">
            @if($p->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
                <span class="badge badge--sold">SOLD</span>
            @endif
        </a>
        <div class="card_body">
            <h3 class="card_title">{{ $p->title }}</h3>
            <p class="card_price">¥{{ number_format($purchase->amount) }}</p>
        </div>
        </article>
        @empty
        <p class="muted">購入履歴はありません。</p>
    @endforelse
    </div>
@endif
</div>
@endsection