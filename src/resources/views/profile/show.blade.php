@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="container">
    <h1 class="page-title">マイページ</h1>

{{-- ユーザー見出し --}}
<div class="user-head">
    <img src="{{ $user->avatarUrl }}" alt="{{ $user->name }}" class="avatar">

    <div class="user-meta">
    <div class="user-name">{{ $user->name }}</div>
    <div class="muted">プロフィール</div>
    </div>

    <a href="{{ route('profile.edit') }}" class="btn btn--ghost btn--sm" style="margin-left:auto;">
        プロフィールを編集
    </a>
</div>

{{-- タブ --}}
<div class="tabs">
    <a href="#my"  class="tab is-active">出品した商品</a>
    <a href="#buy" class="tab">購入した商品</a>
</div>

{{-- 出品した商品 --}}
<section id="my" class="panel mt-20">
    <h2 class="section-title">出品した商品</h2>

    @if ($myProducts->isNotEmpty())
    <div class="cards">
        @foreach ($myProducts as $product)
<article class="card">
    <a href="{{ route('products.show', $product) }}" class="card_thumb">
    <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_img">
    @if ($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
    <span class="badge badge--sold">SOLD</span>
    @endif
    </a>

<div class="card_body">
    <h3 class="card_title">{{ $product->title }}</h3>
    <p class="card_price">¥{{ number_format($product->price) }}</p>
</div>
</article>
@endforeach
</div>
    @else
        <p class="muted">出品商品はありません。</p>
    @endif
</section>

{{-- 購入した商品 --}}
<section id="buy" class="panel mt-20">
    <h2 class="section-title">購入した商品</h2>

    @forelse ($purchases as $purchase)
        @php($product = $purchase->product)
        <article class="purchase-item">
        <img src="{{ $product->image_url }}" alt="" class="purchase-thumb">
        <div class="purchase-meta">
            <h3 class="purchase-title">{{ $product->title }}</h3>
            <p class="purchase-sub">
            ¥{{ number_format($purchase->amount) }} /
            {{ $purchase->created_at->format('Y-m-d') }}
            </p>
        </div>
    </article>
    @empty
        <p class="muted">購入履歴はまだありません。</p>
    @endforelse
</section>
</div>
@endsection