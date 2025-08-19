@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2>購入完了</h2>
    <p>ご購入ありがとうございました！</p>
    <a href="{{ route('products.index') }}" class="buy-button">商品一覧へ戻る</a>
</div>
@endsection