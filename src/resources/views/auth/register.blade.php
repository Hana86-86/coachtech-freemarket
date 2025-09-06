@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="container container--narrow">
    <h2 class="page-title">会員登録</h2>

    @if (session('success'))
    <div class="flash flash--success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="flash flash--error">
        <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
@endif

    <form method="POST" action="{{ route('register') }}" class="form" novalidate>
        @csrf

    <div class="form_group">
        <label class="form_label">ユーザー名</label>
        <input type="text" name="name" class="form_control" value="{{ old('name') }}" required autocomplete="username" inputmode="text">
        @error('name') <p class="error">{{ $message }}</p> @enderror
    </div>
    <div class="form_group">
        <label class="form_label">メールアドレス</label>
        <input type="email" name="email" class="form_control" value="{{ old('email') }}" required autocomplete="email" inputmode="email">
        @error('email') <p class="error">{{ $message }}</p> @enderror
    </div>
    <div class="form_group">
        <label class="form_label">パスワード</label>
        <input type="password" name="password" class="form_control" required>
        @error('password') <p class="error">{{ $message }}</p>@enderror
    </div>
    <div class="form_group">
        <label class="form_label">確認用パスワード</label>
        <input type="password" name="password_confirmation" class="form_control" required>
        @error('password_confirmation') <p class="error">{{ $message }}</p> @enderror
    </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">登録する</button>
    </form>
    <div class="link-wrapper">
    <a href="{{ route('login') }}" class="link-like">ログインはこちら</a>
</div>
</div>
@endsection