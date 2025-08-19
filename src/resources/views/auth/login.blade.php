@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="form-container">
    <h2 class="form-title">ログイン</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <label for="email">メールアドレス</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <p class="error">{{ $message }}</p> @enderror

        <label for="password">パスワード</label>
        <input type="password" name="password" required>
        @error('password') <p class="error">{{ $message }}</p> @enderror

        <button type="submit">ログイン</button>
    </form>
    <div class="link-wrapper">
    <a href="{{ route('register') }}" class="link-text">会員登録はこちら</a>
</div>
</div>
@endsection