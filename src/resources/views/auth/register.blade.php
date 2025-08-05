@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="form-container">
    <h2>会員登録</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>ユーザー名</label>
        <input type="text" name="name" required>

        <label>メールアドレス</label>
        <input type="email" name="email" required>

        <label>パスワード</label>
        <input type="password" name="password" required>

        <label>パスワード確認</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">登録する</button>
    </form>
</div>
@endsection