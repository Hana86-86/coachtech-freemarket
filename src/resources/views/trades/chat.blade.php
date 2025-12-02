{{-- resources/views/trades/chat.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="trade-chat">

    {{-- 左カラム：その他の取引一覧 --}}
    <aside class="trade-chat__sidebar">
        <h2 class="trade-chat__sidebar-title">その他の取引</h2>

        <ul class="trade-chat__thread-list">
            @forelse ($otherTrades as $other)
            @php $p = $other->product; @endphp

            <li class="trade-chat__thread {{ $other->id === $purchase->id ? 'trade-chat__thread--active' : '' }}">
                <a href="{{ route('trades.chat.show', $other) }}">
                    <div class="trade-chat__thread-user">
                        {{ $p->title }}
                    </div>
                    <div class="trade-chat__thread-product">
                        商品名：{{ $p->title }}
                    </div>
                </a>
            </li>
            @empty
            <li class="trade-chat__thread-empty">他の取引はありません。</li>
            @endforelse
        </ul>
    </aside>

    {{-- ===== 右側：メインエリア ===== --}}
    <main class="trade-chat__main">

        {{-- 上部：商品情報ヘッダー --}}
        <header class="trade-chat__header">
            <div class="trade-chat__partner">
                {{-- ★ 後で「相手ユーザー名」を差し替える --}}
                <span class="trade-chat__partner-label">取引相手：</span>
                <span class="trade-chat__partner-name">ユーザー名 さんとの取引画面</span>
            </div>

            <div class="trade-chat__product">
                <div class="trade-chat__product-name">商品名：{{ $product->title}}</div>
                <div class="trade-chat__product-price">商品価格：¥{{ number_format($purchase->amount) }}</div>
            </div>

            {{-- 購入者・出品者向けラベル（Figma の上部タグのイメージ） --}}
            <div class="trade-chat__role-label">
                あなたは <span class="trade-chat__role">購入者</span> として参加しています
            </div>
        </header>

        {{-- 中央：メッセージ一覧 --}}
        <section class="trade-chat__messages">
            @foreach ($messages as $message)
            @php
            $isMine = $message->user_id === auth()->id();
            @endphp
            {{-- 自分のメッセージ（右寄せ） --}}
            <div class="trade-chat__message trade-chat__message--me">
                <div class="trade-chat__message-body">
                    {{ $message->body }}
                </div>
                <div class="trade-chat__message-meta">
                    {{ $message->user->name ?? 'ユーザー' }}
                    ／
                    {{ $message->created_at->format('Y/m/d H:i') }}
                </div>
                @endforeach
            </div>

            {{-- 相手のメッセージ（左寄せ） --}}
            <div class="trade-chat__message trade-chat__message--partner">
                <div class="trade-chat__message-body">
                    ご購入ありがとうございます。発送までしばらくお待ちください。
                </div>
                <div class="trade-chat__message-meta">
                    <span class="trade-chat__message-author">ユーザー名</span>
                    <span class="trade-chat__message-time">2025/12/01 10:32</span>
                </div>
            </div>

            {{-- 取引完了メッセージ（システムメッセージ風） --}}
            <div class="trade-chat__system-message">
                取引が完了しました。
            </div>
        </section>

        {{-- 下部：入力フォーム --}}
        <section class="trade-chat__input-area">
            <form
                class="trade-chat__form"
                method="POST"
                action="{{ route('trades.messages.store', $purchase) }}">
                @csrf

                <textarea
                    name="body"
                    class="trade-chat__textarea"
                    placeholder="メッセージを入力してください">{{ old('body') }}</textarea>

                @error('body')
                <p class="form-error">{{ $message }}</p>
                @enderror

                <div class="trade-chat__form-footer">
                    <button type="submit" class="trade-chat__send-button">
                        送信する
                    </button>
                </div>
            </form>
        </section>

        {{-- 評価エリア --}}
        @php
        $isBuyer = auth()->id() === $purchase->buyer_id;
        $isSeller = auth()->id() === $purchase->product->user_id;
        @endphp

        @if($isBuyer || $isSeller)
        <section class="trade-chat__rating">
            <p>取引が完了しました。</p>
            <p class="muted">
                @if($isBuyer)
                この取引で <strong>出品者 {{ optional($purchase->product->user)->name ?? '（出品者不明）' }}</strong> を評価してください。
                @else
                この取引で <strong>購入者 {{ optional($purchase->buyer)->name ?? '（購入者未設定）' }}</strong> を評価してください
                @endif
            </p>
            <form action="{{ route('trades.rating', $purchase) }}" method="post">
                @csrf
                <select name="rating">
                    <option value="">評価を選択してください</option>
                    @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}">{{ $i }}（★{{ str_repeat('★', $i) }}）</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn--primary">評価を送信する</button>
            </form>
        </section>
        @endif

        {{-- まだ評価していない時だけフォームを表示 --}}
        @if (is_null($purchase->buyer_rating))
        <form method="post" action="{{ route('trades.rating.store', $purchase) }}" class="trade-rating-form">
            @csrf

            <div class="trade-rating-form__stars">
                {{-- ★ 1〜5 をラジオボタンで選択 --}}
                @for ($i = 1; $i <= 5; $i++)
                    <label class="trade-rating-form__star">
                    <input
                        type="radio"
                        name="rating"
                        value="{{ $i }}"
                        class="trade-rating-form__input"
                        {{-- old() でバリデーションエラー後も選択を維持。初期値は 5 にしておく --}}
                        @checked(old('rating', 5)==$i)>
                    ★
                    </label>
                    @endfor
            </div>

            {{-- バリデーションエラー表示 --}}
            @error('rating')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn btn--primary rating-submit">
                評価を送信する
            </button>
        </form>
        @else
        {{-- すでに評価済みの場合の表示 --}}
        <p class="mt-2">
            あなたの評価：
            <span class="rating-value">
                {{ str_repeat('★', $purchase->buyer_rating) }}
                {{ str_repeat('☆', 5 - $purchase->buyer_rating) }}
            </span>
            （{{ $purchase->buyer_rating }} / 5）
        </p>
        @endif
        </section>
    </main>
</div>

@endsection