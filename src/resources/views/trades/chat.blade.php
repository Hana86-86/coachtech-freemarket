{{-- resources/views/trades/chat.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="trade-chat">

    {{-- ===== 左側：サイドバー（他の取引一覧など） ===== --}}
    <aside class="trade-chat__sidebar">
        <div class="trade-chat__sidebar-header">
            <h2 class="trade-chat__sidebar-title">その他の取引</h2> {{-- 左上の「その他の取引」エリア --}}
        </div>

        <ul class="trade-chat__thread-list">
            {{-- ★ここは後で「他の取引リスト」を動的に表示する場所 --}}
            <li class="trade-chat__thread trade-chat__thread--active">
                <div class="trade-chat__thread-user">ユーザーAさん</div>
                <div class="trade-chat__thread-product">商品名：腕時計</div>
            </li>
            <li class="trade-chat__thread">
                <div class="trade-chat__thread-user">ユーザーBさん</div>
                <div class="trade-chat__thread-product">商品名：ノートPC</div>
            </li>
            <li class="trade-chat__thread">
                <div class="trade-chat__thread-user">ユーザーCさん</div>
                <div class="trade-chat__thread-product">商品名：マイク</div>
            </li>
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
                {{-- ★ 後で「商品名・価格」を差し替える --}}
                <div class="trade-chat__product-name">商品名：マイク</div>
                <div class="trade-chat__product-price">商品価格：¥8,000</div>
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

        {{-- モーダル：評価入力（Figma の星評価ダイアログ）※まだ JS なし・静的 --}}
        <div class="trade-chat__rating-modal">
            <div class="trade-chat__rating-modal-inner">
                <p class="trade-chat__rating-title">取引が完了しました。</p>
                <p class="trade-chat__rating-sub">
                    この取引を評価してください。
                </p>

                <div class="trade-chat__rating-stars">
                    ★★★★☆ {{-- ★ ここはあとでクリックで変わるようにする想定 --}}
                </div>

                <button class="trade-chat__rating-submit">
                    評価を送信する
                </button>
            </div>
        </div>

    </main>
</div>

@endsection