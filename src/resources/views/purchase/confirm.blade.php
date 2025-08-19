@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2>購入確認</h2>

    <div class="product-image-wrapper">
        <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}" style="width: 100%; max-width: 300px;">
    </div>

    <p><strong>商品名: </strong>{{ $product->title }}</p>
    <p><strong>カテゴリ: </strong>{{ optional($product->category)->name ?? '未設定' }}</p>
    <p><strong>状態: </strong>{{ $product->condition }}</p>
    <p><strong>説明: </strong>{{ $product->description }}</p>

    @php
        $profile = auth()->user()->profile ?? null;
    @endphp
    
    <p><strong>価格: </strong>¥{{ number_format($product->price) }}</p>
    <p><strong>小計:</strong>¥{{ number_format($product->price) }}</p>

    {{-- 支払い方法の選択 （プルダウン）--}}
    <form method="GET" action="{{ route('purchase.confirm', $product->id) }}" style="margin: 12px 0;">
        <label for="method">支払い方法</label>
        <select id="method" name="method" onchange="this.form.submit()">
            <option value="card" {{ $method==='card' ? 'selected' : '' }}>カード支払い</option>
            <option value="konbini" {{ $method==='konbini' ? 'selected' : '' }}>コンビニ支払い</option>
        </select>
    </form>
    <p style="margin-top:8px;">
        選択中の支払い方法：<strong>
            {{ $method === 'konbini' ? 'コンビニ払い' : 'カード支払い' }}
        </strong>
    </p>

    <h3 style="margin-top:20px;">配送先</h3>
    @if ($profile && $profile->address)
        <p>〒{{ $profile->postal_code }}</p>
        <p>{{ $profile->address }} {{ $profile->building }}</p>
    @else
        <p>配送先が未確定です</p>
    @endif

    <div style="margin-top:10px;">
        <a class="link-text" href="{{ route('purchase.address.edit', ['product_id' => $product->id]) }}">
            変更する
        </a>
    </div>

    {{-- Stripeに進むボタン --}}
    <form method="POST" action="{{ route('purchase.checkout', $product->id) }}" style="margin-top: 16px;">
        @csrf
        <input type="hidden" name="payment_method" value="{{ $method }}">
        <button type="submit" class="buy-button">購入を確定して支払いへ</button>
    </form>

    <div class="link-wrapper">
        <a href="{{ route('products.show', $product->id) }}" class="link-text">戻る</a>
    </div>
</div>
@endsection