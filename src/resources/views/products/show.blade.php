@extends('layouts.app')

@section('content')
<div class="product-detail-container-vertical">
    <div class="product-detail panel">
        @if ($product->image_path)
            <img src="{{ asset($product->image_path) }}" alt="{{ $product->title }}">
        @endif

        @if($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
        <span class="ribbon-sold" aria-hidden="true">SOLD</span>
        @endif
        </div>

        @if ($product->sale_status === \App\Models\Product::SALE_STATUS_SOLD)
            <a class="buy-button is-disabled" aria-disabled="true">購入する</a>
        @else
            <a href="{{ route('purchase.confirm', $product->id) }}" class="buy-button">購入する</a>
        @endif

        <div class="product-info">
            <h2 class="product-title">{{ $product->title }}</h2>

            <div class="like-area">
                <span class="like-count">{{ $product->favorites_count  ?? $product->favorites()->count() }}</span>
                @auth
                @if(auth()->user()->favorites()->where('product_id', $product->id)->exists())
                    <form action="{{ route('favorites.destroy', $product->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn--ghost">❤︎ いいね解除</button>
                    </form>
                    @else
                    <form action="{{ route('favorites.store', $product->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button class="btn btn--ghost">♡ いいね</button>
                    </form>
                @endif
                @endauth
            </div>
        </div>
            <p class="product-price">¥{{ number_format($product->price) }}</p>
            <p><strong>カテゴリ: </strong>{{ optional($product->category)->name }}</p>
            <p><strong>状態: </strong>{{ $product->condition }}</p>
            <p><strong>説明: </strong>{{ $product->description }}</p>

        {{-- コメント--}}
        <section class="panel mt-20">
            <h3 class="section-title">コメント ({{ $product->comments_count ?? $product->comments()->count() }})</h3>

            <ul class="comment-list">
                @forelse($product->comments as $comment)
                <li class="comment">
                    <div class="comment-row">
                        <div class="comment-main">
                            <p class="comment-meta">{{ $comment->user->name }} . {{ $comment->created_at->diffForHumans() }}</p>
                            <p class="comment-body">{{ $comment->body }}</p>
                        </div>
                    @can('delete', $comment)
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
                        @csrf @method('DELETE')
            <button type="submit" class="comment-delete"
            onclick="return confirm('コメントを削除しますか？')">削除</button>
            </form>
        @endcan
        </div>
        </li>
    @empty
        <li>まだコメントはありません。</li>
    @endforelse
    </ul>

    @auth
    <form action="{{ route('comments.store', $product) }}" method="POST" class="mt-12">
    @csrf
    <textarea name="body" rows="3" class="textarea" placeholder="コメントを入力（必須・255文字まで）">{{ old('body') }}</textarea>
    @error('body') <p class="error">{{ $message }}</p> @enderror
    <button class="btn btn--primary mt-8">コメントを投稿</button>
    </form>
    @else
    <p class="mt-12">コメントするにはログインしてください。</p>
    @endauth
</section>
</div>
@endsection

