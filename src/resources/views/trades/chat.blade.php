{{-- resources/views/trades/chat.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="trade-chat">

    {{-- ================= 左カラム：その他の取引一覧 ================= --}}
    <aside class="trade-chat__sidebar">
        <h2 class="trade-chat__sidebar-title">その他の取引</h2>

        <ul class="trade-chat__thread-list">
            @forelse ($otherTrades as $other)
            @php
            // 他の取引の商品のショートカット
            $p = $other->product;
            @endphp

            <li class="trade-chat__thread {{ $other->id === $purchase->id ? 'trade-chat__thread--active' : '' }}">
                {{-- クリックで、その取引のチャット画面へ遷移 --}}
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

    {{-- ================= 右カラム：メインエリア ================= --}}
    <main class="trade-chat__main">

        {{-- ---------- 上部：商品情報ヘッダー ---------- --}}
        <header class="trade-chat__header">
            <div class="trade-chat__partner">
                {{-- ★ 今はダミー文言。あとで「相手ユーザー名」に差し替える予定 --}}
                <span class="trade-chat__partner-label">取引相手：</span>
                <span class="trade-chat__partner-name">ユーザー名 さんとの取引画面</span>
            </div>

            <div class="trade-chat__product">
                <div class="trade-chat__product-name">
                    商品名：{{ $product->title }}
                </div>
                <div class="trade-chat__product-price">
                    商品価格：¥{{ number_format($purchase->amount) }}
                </div>
            </div>

            {{-- 今は「購入者」と決め打ち。必要なら Buyer/Seller で分岐させる --}}
            <div class="trade-chat__role-label">
                あなたは <span class="trade-chat__role">購入者</span> として参加しています
            </div>
        </header>

        {{-- ---------- 中央：メッセージ一覧 ---------- --}}
        <section class="trade-chat__messages">
            @foreach ($messages as $message)
            @php
            // 自分のメッセージかどうかを判定
            $isMine = $message->user_id === auth()->id();
            @endphp

            {{-- 自分／相手 でクラスを切り替え --}}
            <div class="trade-chat__message {{ $isMine ? 'trade-chat__message--me' : 'trade-chat__message--partner' }}">
                <div class="trade-chat__message-body">
                    {{ $message->body }}
                </div>
                <div class="trade-chat__message-meta">
                    {{ $message->user->name ?? 'ユーザー' }}
                    ／
                    {{ $message->created_at->format('Y/m/d H:i') }}
                </div>

                {{-- ★ 自分のメッセージだけ 編集 / 削除 ボタンを表示（US003） --}}
                @if ($isMine)
                <div class="trade-chat__message-actions">
                    {{-- 編集フォーム --}}
                    <form
                        action="{{ route('trades.messages.update', $message) }}"
                        method="post"
                        class="trade-chat__message-edit-form">
                        @csrf
                        @method('patch')

                        <input
                            type="text"
                            name="body"
                            value="{{ old('body', $message->body) }}"
                            class="trade-chat__message-input">

                        <button type="submit" class="btn btn--sm">
                            編集を保存
                        </button>
                    </form>

                    {{-- 削除フォーム --}}
                    <form
                        action="{{ route('trades.messages.destroy', $message) }}"
                        method="post"
                        class="trade-chat__message-delete-form"
                        onsubmit="return confirm('このメッセージを削除しますか？');">
                        @csrf
                        @method('delete')

                        <button type="submit" class="btn btn--sm btn--ghost">
                            削除
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </section>

        {{-- ---------- 下部：入力フォーム ---------- --}}
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

        {{-- ---------- 評価エリア（US002） ---------- --}}
        @if (is_null($purchase->buyer_rating))
        {{-- まだ評価していない時だけフォームを表示 --}}
        <section class="trade-chat__rating">
            <p>取引が完了しました。</p>
            <p class="muted">この取引を評価してください。</p>

            <form method="post"
                action="{{ route('trades.rating.store', $purchase) }}"
                class="trade-rating-form">
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
                            {{-- old() でエラー後も選択維持。初期値は 5 --}}
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
        </section>
        @else
        {{-- すでに評価済みの場合の表示 --}}
        <section class="trade-chat__rating">
            <p>
                あなたの評価：
                <span class="rating-value">
                    {{ str_repeat('★', $purchase->buyer_rating) }}
                    {{ str_repeat('☆', 5 - $purchase->buyer_rating) }}
                </span>
                （{{ $purchase->buyer_rating }} / 5）
            </p>
        </section>
        @endif

    </main>
</div>
@endsection