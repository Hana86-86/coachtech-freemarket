@php
$isEdit = isset($mode) && $mode === 'edit';
$product = $product ?? null;
@endphp


@extends('layouts.app')

@section('title', $isEdit ? '商品を編集' : '出品')

@section('content')
<div class="sell-container container--narrow">
    <h1 class="page-title">{{ $isEdit ? '商品を編集' : '出品' }}</h1>

    <form class="sell-form" action="{{ $isEdit ? route('products.update',$product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
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
            <label class="image-drop"> {{ $isEdit ? '差し替えは任意です' : '必須' }}</label>
                <input type="file" name="image" accept="image/*" >
                @error('image') <p class="error">{{ $message }}</p> @enderror
                <span class="image-drop__hint">画像を選択する</span>
        </section>

    {{-- 詳細 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品の詳細</h2>

    {{-- カテゴリー --}}
<div class="form-row">
<span class="form-label">カテゴリ</span>

<div class="chip-group" role="radiogroup" aria-label="カテゴリ">
    @foreach($categories as $cat)
    @php
        $checked = (string)old('category_id', $product->category_id ?? '') === (string)$cat->id;
    @endphp

    <input
        type="radio"
        id="cat-{{ $cat->id }}"
        name="category_id"
        value="{{ $cat->id }}"
        class="chip_input"
        {{ $checked ? 'checked' : '' }}
    >
    <label for="cat-{{ $cat->id }}" class="chip">{{ $cat->name }}</label>
    @endforeach
</div>

@error('category_id') <p class="error">{{ $message }}</p> @enderror
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
@endsection