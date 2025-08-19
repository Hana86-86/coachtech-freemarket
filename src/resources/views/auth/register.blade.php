@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="form-container">
    <h2>会員登録</h2>
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <label>ユーザー名</label>
        <input type="text" name="name" required>
        @error('name') <p class="error">{{ $message }}</p> @enderror

        <label>メールアドレス</label>
        <input type="email" name="email" required>
        @error('email') <p class="error">{{ $message }}</p> @enderror

        <label>パスワード</label>
        <input type="password" name="password" required>
        @error('password') <p class="error">{{ $message }}</p>@enderror

        <label>パスワード確認</label>
        <input type="password" name="password_confirmation" required>
        @error('password_confirmation') <p class="error">{{ $message }}</p> @enderror

        <button type="submit">登録する</button>
    </form>
    <div class="link-wrapper">
    <a href="{{ route('login') }}" class="link-text">ログインはこちら</a>
</div>
</div>
@endsection