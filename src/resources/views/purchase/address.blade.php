@extends('layouts.app')
@section('title','住所の変更')

@section('content')
<div class="container container--narrow">
<h1 class="page-title">住所の変更</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('purchase.address.update') }}" class="address-form">
    @csrf
    <input type="hidden" name="product_id" value="{{ $productId }}">

    <div class="form-rail">

    <label class="form-label">郵便番号</label>
    <input type="text" name="postal_code" class="input"
            value="{{ old('postal_code', $profile->postal_code ?? '') }}" required>
    @error('postal_code') <p class="error">{{ $message }}</p> @enderror

    <label class="form-label" style="margin-top:12px;">住所</label>
    <input type="text" name="address" class="input"
            value="{{ old('address', $profile->address ?? '') }}" required>
    @error('address') <p class="error">{{ $message }}</p> @enderror

    <label class="form-label" style="margin-top:12px;">建物名（任意）</label>
    <input type="text" name="building" class="input"
            value="{{ old('building', $profile->building ?? '') }}">
    @error('building') <p class="error">{{ $message }}</p> @enderror

    <button type="submit" class="btn btn--primary btn--form" style="margin-top:16px;">
        更新する
    </button>

    </div>
</form>
</div>
@endsection