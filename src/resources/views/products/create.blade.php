@php
$isEdit = isset($mode) && $mode === 'edit';
$product = $product ?? null;
@endphp


@extends('layouts.app')

@section('title', $isEdit ? '商品を編集' : '出品')

@section('content')
<div class="sell-container container--narrow">
    <h1 class="page-title">{{ $isEdit ? '商品を編集' : '出品' }}</h1>

    <form class="sell-form" action="{{ $isEdit ? route('products.update',$product) : route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

    {{-- 商品画像 --}}
    @if($isEdit && $product?->image_path)
        <div class="mb-2">
        <img src="{{ $product->image_url }}" alt="" style="max-width:160px;border-radius:8px;">
        </div>
    @endif
<section class="sell-block">
    <h2 class="sell-block__title">商品画像</h2>

    <div class="image-drop" id="imageDrop">
    <img id="imagePreview" class="image-drop__preview" alt="プレビュー" style="display:none;" />
    <input id="imageInput" type="file" name="image" accept="image/*" class="image-drop__input">
    <label for="imageInput" class="image-drop__hint">ファイルを選択</label>
</div>

    @error('image') <p class="error">{{ $message }}</p> @enderror
</section>
    {{-- 詳細 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品の詳細</h2>

    {{-- カテゴリ --}}
<div class="chips" role="group" aria-label="カテゴリー">
    @php
    // 編集時は関連のID配列、作成時は old()
    $checkedIds = old('category_id', isset($product)
        ? $product->categories->pluck('id')->all()
        : []);
    @endphp

    @foreach($categories as $cat)
    <input
        type="checkbox"
        id="cat-{{ $cat->id }}"
        name="category_id[]"
        value="{{ $cat->id }}"
        class="chip_input"
        @checked(in_array($cat->id, $checkedIds, true))
    >
    <label for="cat-{{ $cat->id }}" class="chip">{{ $cat->name }}</label>
    @endforeach
    @error('category_id') <p class="error">{{ $message }}</p> @enderror
    @error('category_id.*') <p class="error">{{ $message }}</p> @enderror
</div>

{{-- 状態：プルダウン --}}
<div class="form-row">
    <label class="form-label">商品の状態</label>
    <select name="condition" class="select">
    <option value="">選択してください</option>
    @foreach($conditions as $condition)
    <option value="{{ $condition }}"
        @selected(old('condition', $product->condition ?? '') == $condition)>
        {{ $condition }}
    </option>
    @endforeach
</select>
    @error('condition') <p class="error">{{ $message }}</p> @enderror
</div>

        {{-- 商品名・ブランド・説明・価格 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品名と説明</h2>

            <div class="form-row">
                <label class="form-label">商品名</label>
                <input type="text" name="title" value="{{ old('title', $product->title ?? '') }}" class="input">
                @error('title') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">ブランド名</label>
                <input type="text" name="brand" value="{{ old('brand', $product->brand ?? '' ) }}" class="input">
                @error('brand') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">商品の説明</label>
                <textarea name="description" rows="4" class="textarea">{{ old('description', $product->description ?? '' ) }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">販売価格</label>
                <div class="input-price">
                    <span>¥</span>
                    <input type="number" name="price" min="1" step="1" value="{{ old('price',$product->price ?? '' ) }}" class="input input--price">
                </div>
                @error('price') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="sell-actions">
            <button class="btn btn--primary mt-6">{{ $isEdit ? '更新する' : '出品する' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');
    const drop = document.getElementById('imageDrop');

  // 画像選択 → 即プレビュー
    input?.addEventListener('change', () => {
    const file = input.files && input.files[0];
    if (!file) {
    preview.style.display = 'none';
    preview.src = '';
    drop.classList.remove('has-image');
    return;
    }
    const url = URL.createObjectURL(file);
    preview.src = url;
    preview.onload = () => URL.revokeObjectURL(url);
    preview.style.display = 'block';
    drop.classList.add('has-image');
    });

  // 枠クリックでもファイル選択を開く
drop?.addEventListener('click', (e) => {
    const target = e.target;
    // すでに input/label を直接クリックした場合は何もしない
    if (target.tagName.toLowerCase() === 'input' || target.tagName.toLowerCase() === 'label') return;
    input?.click();
    });
});
</script>
@endpush
@endsection