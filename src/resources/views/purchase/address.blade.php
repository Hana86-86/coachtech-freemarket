@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2>住所の変更</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('purchase.address.update') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $productId }}">

        <label>郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}">
        @error('postal_code')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <label>住所</label>
        <input type="text" name="address" value="{{ old('address', $profile->address) }}">
        @error('address')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <label>建物名</label>
        <input type="text" name="building" value="{{ old('building', $profile->building) }}">
        @error('building')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <button type="submit">更新</button>
    </form>

    <div class="link-wrapper">
        <a class="link-text" href="{{ $productId ? route('purchase.confirm',$productId) : route('products.index') }}">
            戻る
        </a>
    </div>
</div>
@endsection