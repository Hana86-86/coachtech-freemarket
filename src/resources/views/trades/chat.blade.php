@extends('layouts.app')

@php
$user = auth()->user();

$isBuyer = $user && $purchase->buyer_id === $user->id;

$isSeller = $user && $purchase->product->user_id === $user->id;

$canRateAsBuyer = $isBuyer
&& $purchase->status === 'completed'
&& is_null($purchase->buyer_rating);

$canRateAsSeller = $isSeller
&& $purchase->status === 'completed'
&& ! is_null($purchase->buyer_rating)
&& is_null($purchase->seller_rating);

$showRatingModal = $canRateAsBuyer || $canRateAsSeller;
@endphp
@section('content')
<div class="trade-chat">

  {{-- ================= 左サイドバー ================= --}}
  <aside class="trade-chat__sidebar">
    <p class="trade-chat__sidebar-title">その他の取引</p>

    <ul class="trade-chat__thread-list">
      @forelse ($otherTrades as $other)
      @php
      $p = $other->product;
      @endphp

      <li class="trade-chat__thread {{ $other->id === $purchase->id ? 'trade-chat__thread--active' : '' }}">
        <a href="{{ route('trades.chat.show', $other) }}" class="trade-chat__thread-link">
          <div class="trade-chat__thread-title">
            {{ $p->title ?? '商品名' }}
          </div>
          <div class="trade-chat__thread-meta">
            {{ $partner->name ?? 'ユーザー名' }}
          </div>
        </a>
      </li>
      @empty
      <li class="trade-chat__thread-empty">
        他の取引はありません。
      </li>
      @endforelse
    </ul>
  </aside>

  {{-- ================= 右側：メインエリア ================= --}}
  <main class="trade-chat__main">

    <header class="trade-chat__head">
      <div class="trade-chat__head-left">
        <div class="trade-chat__head-avatar">
          @php
          $roleMark = match ($role) {
          'buyer' => '購',
          'seller' => '出',
          default => '？',
          };
          @endphp

          <span class="role-avatar role-avatar--{{ $role === 'buyer' ? 'buyer' : 'seller' }}">
            {{ $roleMark }}
          </span>
        </div>

        <div class="trade-chat__head-text">
          <p class="trade-chat__head-title">
            {{ $partner->name ?? 'ユーザー名' }} さんとの取引画面
          </p>

          @php
          $roleLabel = match ($role) {
          'buyer' => '購入者として',
          'seller' => '出品者として',
          default => 'ゲストとして',
          };
          @endphp

          <p class="trade-chat__head-role">
            <span class="trade-chat__head-role-label">{{ $roleLabel }}</span>
            参加しています
          </p>
        </div>
      </div>

      {{-- 右側：取引を完了するボタン --}}
      @if($isBuyer && $purchase->status === 'trading')
      <div class="trade-chat__head-action">
        <form method="POST" action="{{ route('trades.complete', $purchase) }}">
          @csrf
          <button type="submit" class="btn btn-primary trade-chat__complete-btn">
            取引を完了する
          </button>
        </form>
      </div>
      @endif
    </header>
    {{-- ---------- 上部：商品カード（画像＋商品名＋価格＋ボタン） ---------- --}}
    <section class="trade-chat__product-block">
      {{-- 商品画像 --}}
      <div class="trade-chat__product-image-wrap">
        @if (!empty($product->image_path))
        <img
          src="{{ $product->image_url }}"
          alt="{{ $product->title }}"
          class="trade-chat__product-image">
        @endif
      </div>

      {{-- 商品情報（タイトル・価格・ボタン） --}}
      <div class="trade-chat__product-info">
        <h2 class="trade-chat__product-title">
          {{ $product->title }}
        </h2>

        <p class="trade-chat__product-price">
          ¥{{ number_format($product->price) }}
        </p>

      </div>
    </section>

    {{-- ---------- 中央：メッセージ一覧 ---------- --}}
    <section class="trade-chat__messages">
      @foreach ($messages as $message)
      @php
      $isMine = $message->user_id === auth()->id();
      @endphp

      {{-- 自分／相手 でクラスを切り替え --}}
      <article class="trade-chat__message {{ $isMine ? 'trade-chat__message--me' : 'trade-chat__message--partner' }}">
        <div class="trade-chat__message-body">
          {{-- 本文（あれば表示） --}}
          @if ($message->body)
          <p>{{ $message->body }}</p>
          @endif

          {{-- 画像（あれば表示） --}}
          @if ($message->image_url)
          <div class="trade-chat__message-image">
            <img src="{{ $message->image_url }}" alt="送信画像">
          </div>
          @endif
        </div>

        {{-- 送信者名 + 日時 --}}
        <div class="trade-chat__message-meta">
          {{ $message->user->name ?? 'ユーザー' }}
          ／
          {{ $message->created_at->format('Y/m/d H:i') }}
        </div>

        {{-- 自分のメッセージだけ 編集 / 削除 ボタンを表示 --}}
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
      </article>
      @endforeach
    </section>

    {{-- ---------- 下部：入力フォーム（本文 + 画像送信） ---------- --}}
    <section class="trade-chat__input-area">
      <form
        class="trade-chat__form"
        method="POST"
        action="{{ route('trades.messages.store', $purchase) }}"
        enctype="multipart/form-data">
        @csrf

        {{-- テキスト本文 --}}
        @error('body')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <label class="trade-chat__textarea-label">
          <textarea
            name="body"
            class="trade-chat__textarea"
            placeholder="取引メッセージを記入してください">{{ old('body') }}</textarea>
        </label>


        @error('image')
        <p class="form-error">{{ $message }}</p>
        @enderror
        {{-- 画像アップロード --}}
        <div class="trade-chat__upload">
          <label class="trade-chat__upload-label">
            <span class="trade-chat__upload-label-main">画像を追加</span>
            <input type="file" name="image" accept="image/jpeg,image/png">
          </label>

        </div>

        <div class="trade-chat__form-footer">
          <button type="submit" class="trade-chat__send-icon-btn"></button>
        </div>
      </form>
    </section>

    {{-- ---------- 評価エリア：モーダル ---------- --}}
    @if ($showRatingModal)
    {{-- 評価していない場合だけモーダルを描画 --}}
    <div id="ratingModal" class="rating-modal">
      <div class="rating-modal__overlay"></div>

      <div class="rating-modal__content">
        <button type="button" class="rating-modal__close" id="ratingModalClose">
          ×
        </button>

        <h2 class="rating-modal__title">取引が完了しました。</h2>
        <p class="rating-modal__text">今回の取引相手はいかがでしたか？</p>

        <form
          method="post"
          action="{{ route('trades.rating', $purchase) }}"
          class="rating-modal__form">
          @csrf

          <div class="rating-modal__stars">
            @for ($i = 1; $i <= 5; $i++)
              <label class="rating-modal__star">
              <input
                type="radio"
                name="rating"
                value="{{ $i }}"
                class="rating-modal__input"
                @checked(old('rating', 5)==$i) />
              <span class="rating-modal__star-icon">★</span>
              </label>
              @endfor
          </div>

          {{-- バリデーションエラー表示 --}}
          @error('rating')
          <p class="form-error">{{ $message }}</p>
          @enderror

          <button type="submit" class="btn btn--primary rating-modal__submit">
            送信する
          </button>
        </form>
      </div>
    </div>
    @endif

  </main>
</div>

{{-- チャット画面専用のJS --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // ================= 評価モーダル制御 =================
    const ratingModal = document.getElementById('ratingModal');

    if (ratingModal) {
      const closeBtn = document.getElementById('ratingModalClose');

      const openModal = () => {
        ratingModal.classList.add('is-open');
      };

      const closeModal = () => {
        ratingModal.classList.remove('is-open');
      };

      // ページ表示時に自動で開く
      openModal();

      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          closeModal();
        });
      }

      // オーバーレイクリックで閉じる（コンテンツ外をクリックしたとき）
      const overlay = ratingModal.querySelector('.rating-modal__overlay');
      if (overlay) {
        overlay.addEventListener('click', () => {
          closeModal();
        });
      }

      // 星のハイライト制御
      const starInputs = document.querySelectorAll('.rating-modal__input');
      const starIcons = document.querySelectorAll('.rating-modal__star-icon');

      // value（1〜5）に応じて、どこまで黄色にするかを切り替える関数
      const updateStars = (value) => {
        starIcons.forEach((icon, index) => {
          if (index < value) {
            icon.classList.add('is-active'); // 選択された星までをハイライト
          } else {
            icon.classList.remove('is-active');
          }
        });
      };

      // ページ読み込み時：checked のラジオがあれば、その値で初期表示
      const checkedInput = document.querySelector('.rating-modal__input:checked');
      if (checkedInput) {
        updateStars(parseInt(checkedInput.value, 10));
      }

      // ラジオボタン変更時：選ばれた値で星を更新
      starInputs.forEach((input) => {
        input.addEventListener('change', (event) => {
          const value = parseInt(event.target.value, 10);
          updateStars(value);
        });
      });
    }

    // ================= メッセージ入力の下書き保持 =================
    const textarea = document.querySelector('.trade-chat__textarea');

    if (textarea) {
      const draftKey = 'trade_chat_draft_{{ $purchase->id }}';

      const saved = localStorage.getItem(draftKey);
      if (saved) {
        textarea.value = saved;
      }

      textarea.addEventListener('input', () => {
        localStorage.setItem(draftKey, textarea.value);
      });

      const form = document.querySelector('.trade-chat__form');
      if (form) {
        form.addEventListener('submit', () => {
          localStorage.removeItem(draftKey);
        });
      }
    }
  });
</script>
@endsection