@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="products-index">

{{-- ===== タブ（おすすめ / マイリスト） ===== --}}
<nav class="products_tabs">
    <a href="{{ route('products.index', array_merge(request()->query(), ['tab' => 'all', 'page' => 1])) }}"
        class="products_tab {{ request('tab') === null || request('tab') === 'all' ? 'is-active' : '' }}">
        おすすめ
    </a>
    <a href="{{ route('products.index', array_merge(request()->query(), ['tab' => 'mylist', 'page' => 1])) }}"
        class="products_tab {{ request('tab') === 'mylist' ? 'is-active' : '' }}">
        マイリスト
    </a>
</nav>

{{-- ===== 一覧 ===== --}}
    @if ($products->count())
    <div class="cards">
        @foreach ($products as $product)
        <article class="cards__item">
            <a href="{{ route('products.show', $product) }}" class="product-card">

            <div class="card_thumb">
                <img src="{{ $product->image_url }}"alt="{{ $product->title }}" loading="lazy">
                @if ($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
                <span class="badge badge--sold">SOLD</span>
                @endif
            </div>

            <div class="card_body">
                <h3 class="card_title">{{ $product->title }}</h3>
                <p class="card_price">¥{{ number_format($product->price) }}</p>
            </div>

        </a>
        </article>
    @endforeach
    </div>
@else
    {{-- 空表示 --}}
    <div class="container--narrow" style="margin:24px auto 80px;">
        <p>該当商品はありません。</p>
    </div>
@endif

{{-- ===== ページネーション ===== --}}
    @if (method_exists($products, 'links'))
    <div class="products_pages">
        {{ $products->links() }}
    </div>
@endif

</div>
@endsection