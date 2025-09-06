@extends('layouts.app')

@section('title','購入確認')

@php
$paymentMethod = request('payment_method','card');
$profile  = auth()->user()?->profile;
$shipping = session('checkout_shipping');

$postal  = $shipping['postal_code'] ?? ($profile->postal_code ?? '');
$address = $shipping['address'] ?? ($profile->address ?? '');
$building= $shipping['building'] ?? ($profile->building ?? '');
$hasAddr  = filled($postal) && filled($address);
@endphp

@section('content')
<div class="pc">  {{-- ←購入確認ページ専用スコープ --}}
    <h1 class="pc__title">購入確認</h1>

<div class="pc__grid">
    {{-- ================= 左カラム ================= --}}
<section class="pc__left">

{{-- 商品情報カード --}}
<article class="pc-card" aria-label="商品情報">
        <div class="pc-card__head">
            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="pc-card__thumb" loading="lazy">
        <div class="pc-card__meta">
            <h2 class="pc-card__title">{{ $product->title }}</h2>
            <p class="pc-price"><span class="pc-price__main">¥{{ number_format($product->price) }}</span><span class="pc-tax-note">（税込）</span></p>
            <p class="pc-meta">カテゴリ：<span class="pc-meta__val">{{ optional($product->category)->name ?? '—' }}</span></p>
            <p class="pc-meta">商品の状態：<span class="pc-meta__val">{{ $product->condition ?? '—' }}</span></p>
            <p class="pc-desc">{{ $product->description }}</p>
        </div>
        </div>
    </article>

 {{-- 支払い方法 --}}
        <section class="pc-panel">
            <h2 class="pc-panel__title">支払い方法</h2>
        <form id="paymentForm" method="GET" action="{{ route('purchase.confirm', $product->id) }}">
        <select name="payment_method" class="pc-select" onchange="this.form.submit()">
            <option value="card"    {{ $paymentMethod==='card'    ? 'selected':'' }}>カード払い</option>
            <option value="konbini" {{ $paymentMethod==='konbini' ? 'selected':'' }}>コンビニ払い</option>
        </select>
        </form>
    </section>

{{-- 配送先 --}}
        <section class="pc-panel">
            <h2 class="pc-panel__title">配送先</h2>
        <div class="pc-address">
            @if(!empty($Shipping))
                <p>{{ $Shipping['postal_code'] }}</p>
                <p>{{ $Shipping['address'] }}</p>
                @if(!empty($Shipping['building'] ))
                <p>{{ $Shipping['building'] }}</p>
                @endif
            @elseif($hasAddr)
            <p>{{ $postal }}</p>
            <p>{{ $address }}</p>
            @if(filled($building))<p>{{ $building }}</p>@endif
            @endif
        </div>
        <div class="pc-panel__actions">
            <a class="btn btn--ghost" href="{{ route('purchase.address.edit', ['product_id'=>$product->id]) }}">変更する</a>
        </div>
    </section>

    </section>

    {{-- ================= 右カラム（サマリー） ================= --}}
    <aside class="pc__right">
    <section class="pc-summary" aria-label="金額と購入">
        <dl class="pc-summary__row">
            <dt>商品代金</dt>
            <dd>¥{{ number_format($product->price) }}</dd>
        </dl>
        <dl class="pc-summary__row">
            <dt>支払い方法</dt>
            <dd>{{ $paymentMethod === 'card' ? 'カード払い' : 'コンビニ払い' }}</dd>
        </dl>

        <form method="POST" action="{{ route('purchase.checkout', $product->id) }}" class="pc-summary__buy">
        @csrf
        <input type="hidden" name="payment_method" value="{{ $paymentMethod }}">
        <button type="submit" class="btn btn--primary btn--xl"
        @unless($hasAddr) disabled aria-disabled="true" @endunless>
    購入手続きへ
</button>
        </form>

        @unless($hasAddr)
        <p class="pc-error">購入には配送先の登録が必要です。</p>
        @endunless
    </section>
    </aside>

</div>
</div>
@endsection