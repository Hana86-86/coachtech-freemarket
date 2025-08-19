@extends('layouts.app')

@section('title','出品')

@section('content')
<div class="sell-container">
    <h1 class="page-title">出品</h1>

    <form class="sell-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品画像</h2>
            <label class="image-drop">
                <input type="file" name="image" accept="image/*" class="image-drop__input">
                <span class="image-drop__hint">画像を選択する</span>
            </label>
            @error('image') <p class="error">{{ $message }}</p> @enderror
        </section>

        {{-- 詳細 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品の詳細</h2>

            {{-- カテゴリー：タグ風だが実体はラジオ --}}
            <div class="category-tags">
                @foreach($categories as $cat)
                    <label class="tag">
                        <input type="radio" name="category_id" value="{{ $cat->id }}" @checked(old('category_id')==$cat->id)>
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('category_id') <p class="error">{{ $message }}</p> @enderror

            {{-- 状態 --}}
            <div class="form-row">
                <label class="form-label">商品の状態</label>
                <select name="condition" class="select">
                    <option value="">選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition }}" @selected(old('condition')===$condition)>{{ $condition }}</option>
                    @endforeach
                </select>
                @error('condition') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- 商品名・ブランド・説明・価格 --}}
        <section class="sell-block">
            <h2 class="sell-block__title">商品名と説明</h2>

            <div class="form-row">
                <label class="form-label">商品名</label>
                <input type="text" name="title" value="{{ old('title') }}" class="input">
                @error('title') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">ブランド名</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="input">
                @error('brand') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">商品の説明</label>
                <textarea name="description" rows="4" class="textarea">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row">
                <label class="form-label">販売価格</label>
                <div class="input-price">
                    <span>¥</span>
                    <input type="number" name="price" value="{{ old('price') }}" class="input input--price" min="1" step="1">
                </div>
                @error('price') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="sell-actions">
            <button type="submit" class="btn btn--primary">出品する</button>
        </div>
    </form>
</div>
@endsection