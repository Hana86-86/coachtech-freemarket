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
      {{-- 取引相手表示 --}}
      <div class="trade-chat__partner">
        <span class="trade-chat__partner-label">取引相手：</span>

        <span class="trade-chat__partner-name">
          @if ($partner)
          {{-- 相手ユーザー名が取得できた場合 --}}
          {{ $partner->name }} さんとの取引画面
          @else
          {{-- 念のためのフォールバック --}}
          （取引相手のユーザー情報が取得できません）
          @endif
        </span>
      </div>

      {{-- 商品画像 + 商品名 + 価格 --}}
      <div class="trade-chat__product">
        @if (!empty($product->image_path))
        <div class="trade-chat__product-image">
          {{-- Product モデル側の getImageUrlAttribute() を想定 --}}
          <img src="{{ $product->image_url }}" alt="{{ $product->title }}"
            class="trade-chat__product-image-img">
        </div>
        @endif

        <div class="trade-chat__product-name">
          商品名：{{ $product->title }}
        </div>
        <div class="trade-chat__product-price">
          商品価格：¥{{ number_format($purchase->amount) }}
        </div>
      </div>

      {{-- 自分の立場（購入者 / 出品者） --}}
      <div class="trade-chat__role-label">
        @php
        $roleLabel = match ($role) {
        'buyer' => '購入者',
        'seller' => '出品者',
        default => 'ゲスト',
        };
        @endphp
        あなたは <span class="trade-chat__role">{{ $roleLabel }}</span> として参加しています
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
        enctype="multipart/form-data"> {{-- ★ ここが超重要 --}}
        @csrf

        {{-- テキスト本文 --}}
        <label class="trade-chat__textarea-label">
          <span>メッセージを入力してください</span>
          <textarea
            name="body"
            class="trade-chat__textarea"
            placeholder="メッセージを入力してください">{{ old('body') }}</textarea>
        </label>

        @error('body')
        <p class="form-error">{{ $message }}</p>
        @enderror

        {{-- 画像アップロード --}}
        <div class="trade-chat__upload">
          <label class="trade-chat__upload-label">
            画像を選択（任意・ jpeg/png）
            <input type="file" name="image" accept="image/jpeg,image/png">
          </label>

          @error('image')
          <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="trade-chat__form-footer">
          <button type="submit" class="trade-chat__send-button">
            送信する
          </button>
        </div>
      </form>
    </section>

    {{-- ---------- 評価エリア：モーダル ---------- --}}
    @if (is_null($purchase->buyer_rating))
    {{-- 評価していない場合だけモーダルを描画 --}}
    <div id="ratingModal" class="rating-modal">
      <div class="rating-modal__overlay"></div>

      <div class="rating-modal__content">
        <button type="button" class="rating-modal__close" id="ratingModalClose">
          ×
        </button>

        <h2 class="rating-modal__title">取引が完了しました。</h2>
        <p class="rating-modal__text">今回の取引はいかがでしたか？</p>

        <form method="post"
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
    @else
    {{-- すでに評価済みの場合の表示（今までと同じ内容でOK） --}}
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
{{-- チャット画面専用のJS --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // 評価モーダル
    const ratingModal = document.getElementById('ratingModal');

    if (ratingModal) {
      const closeBtn = document.getElementById('ratingModalClose');

      const openModal = () => {
        ratingModal.classList.add('is-open');
      };

      const closeModal = () => {
        ratingModal.classList.remove('is-open');
      };

      openModal();

      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          closeModal();
        });
      }

      ratingModal.addEventListener('click', (event) => {
        if (event.target === ratingModal) {
          closeModal();
        }
      });
    }
    // 入力保持
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
    // 星のハイライト制御
    const starInputs = document.querySelectorAll('.rating-modal__input');

    const starIcons = document.querySelectorAll('.rating-modal__star-icon');

    // value（1〜5）に応じて、どこまで黄色にするかを切り替える関数
    const updateStars = (value) => {
      // value より小さい index の星を黄色にする
      starIcons.forEach((icon, index) => {
        if (index < value) {
          icon.classList.add('is-active'); // 黄色クラスを付与
        } else {
          icon.classList.remove('is-active'); // それ以外は外す
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
        const value = parseInt(event.target.value, 10); // "3" → 3 に変換
        updateStars(value);
      });
    });

  });
</script>
@endsection