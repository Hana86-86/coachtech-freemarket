@extends('layouts.app')

@section('title', '商品詳細')

@section('content')
<div class="product-detail container--narrow">

{{-- 左：商品画像だけ --}}
<section class="product-detail__left">
    <div class="card">
        <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="card_img" loading="lazy">
        @if ($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
            <span class="badge badge--sold" aria-hidden="true">SOLD</span>
        @endif
    </div>
</section>

{{-- 右：情報ブロック --}}
<aside class="product-detail__right panel">

    {{-- タイトル・価格・アクション --}}
        <h1 class="product-title">{{ $product->title }}</h1>

        <div class="price_row">
            <div class="price_main">¥{{ number_format($product->price) }}</div>
            <span class="tax-note">（税込）</span>
        </div>

    {{-- 星（いいね）とコメント数 --}}
<div class="meta_bar">
  @auth
    <button
        id="favButton"
        type="button"
        class="fav-btn {{ $isFavorited ? 'is-on' : '' }}"
        aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
        aria-label="お気に入りに{{ $isFavorited ? '登録済み' : '登録' }}"
        data-url="{{ route('favorites.toggle', $product) }}"
    >
        <span id="star" class="fav-btn__icon" aria-hidden="true">
        {{ $isFavorited ? '★' : '☆' }}
        </span>
        <span id="favCount" class="meta_num">
        {{ $product->favorites_count ?? $product->favorites()->count() }}
        </span>
</button>
@endauth

@guest
    {{-- 未ログイン時はログインへ導線 --}}
    <a href="{{ route('login') }}" class="btn btn-ghost"
        aria-label="ログインしてお気に入りに登録する">
        ☆
    </a>
    <span class="meta_num">
        {{ $product->favorites_count ?? $product->favorites()->count() }}
    </span>
@endguest

    <span class="meta_sep"></span>

    <span class="meta_icon" aria-hidden="true">💬</span>
    <span class="meta_num">
    {{ $product->comments_count ?? $product->comments()->count() }}
    </span>
</div>

    {{-- 購入ボタン（売り切れ時は非活性） --}}

        @php
            $isSold = $product->sale_status === \App\Models\Product::SALE_STATUS_SOLD;
            $isOwner = auth()->check() && $product->user_id === auth()->id();
        @endphp
        @if ($isSold || $isOwner)
        <button class="buy-button w-full" disabled aria-disabled="true">購入手続きへ</button>
        @else
        @guest
        <a href="{{ route('login') }}" class="buy-button w-full">購入手続きへ</a>
        @else
        <a href="{{ route('purchase.confirm', $product) }}" class="buy-button w-full">購入手続きへ</a>
        @endguest
    @endif
        {{-- 商品説明 --}}
        <h2 class="section-title mt-24">商品説明</h2>
        <p class="muted">{{ nl2br(e($product->description)) }}</p>

        {{-- 商品の情報（カテゴリ/状態） --}}
        <h2 class="section-title mt-24">商品の情報</h2>
        <dl class="spec">
    <div class="spec__row">
        <dt>カテゴリー</dt>
        <dd>{{ optional($product->category)->name ?? '—' }}</dd>
    </div>
    <div class="spec__row">
        <dt>商品の状態</dt>
        <dd>{{ $product->condition ?? '—' }}</dd>
    </div>
    </dl>
    {{-- コメント --}}
    <h2 class="section-title mt-24">コメント（{{ $product->comments_count ?? $product->comments()->count() }}）</h2>

        @forelse ($product->comments as $comment)
        <div class="comment">
        <div class="comment__head">
            <img class="avatar avatar--sm"
                src="{{ $comment->user->profile->profile_image ? asset('storage/'.$comment->user->profile->profile_image) : asset('images/avatar-placeholder.png') }}"
                alt="{{ $comment->user->name }}">
        <div class="comment__meta">
            <div class="comment__name">{{ $comment->user->name }}</div>
            <div class="muted">{{ $comment->created_at->diffForHumans() }}</div>
        </div>
        </div>
        <p class="comment__body">{{ $comment->body }}</p>
    </div>
    @empty
        <p class="muted">まだコメントはありません。</p>
    @endforelse

    {{-- ログイン中ユーザーのコメント入力 --}}
@auth
@can('create', [\App\Models\Comment::class, $product])
    <div class="comment composer">
        <div class="comment_head">
        <div>
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar avatar--sm"/>
        </div>
        <div class="comment_meta">
            <div class="comment_name">{{ auth()->user()->name }}</div>
            <div class="muted">こちらにコメントが入ります。</div>
        </div>
    </div>

    <form action="{{ route('comments.store', $product) }}" method="POST" class="mt-8">
        @csrf
        <textarea name="body" rows="3" class="comment_input" placeholder="コメントを入力（必須・255文字まで）">{{ old('body') }}</textarea>
        @error('body') <p class="error">{{ $message }}</p> @enderror

        <button type="submit" class="btn btn-primary mt-8">コメントを投稿する</button>
    </form>
    </div>
@else
    {{-- ログインはしているが、ポリシーにより投稿不可（例：売却済み等） --}}
    <p class="muted mt-8">
        この商品にはコメントできません。
        @if($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
        （売却済み）
        @endif
    </p>
@endcan
@else
    {{-- ゲスト表示 --}}
    <p class="muted mt-8">コメントするにはログインしてください。</p>
@endauth

</aside>
</div>

@auth
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('favButton');
        if (!btn) return;

        btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        try {
        const res = await fetch(url, {
                method: 'POST',
                headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
            });

            if (res.status === 401) {
              // ログイン切れなど
                location.href = "{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}";
                return;
            }
            if (!res.ok) return;

            const data = await res.json();

            // ボタン状態の更新
            btn.setAttribute('aria-pressed', data.favorited ? 'true' : 'false');
            btn.classList.toggle('is-on', data.favorited);

            // ★の表示と数の更新
            const star = document.getElementById('star');
            if (star) star.textContent = data.favorited ? '★' : '☆';

            const cnt = document.getElementById('favCount');
            if (cnt) cnt.textContent = data.count;
            } catch (e) {
            console.error(e);
            }
        });
        });
    </script>
    @endpush
@endauth
@endsection