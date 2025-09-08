
@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="container container--narrow">
    <h2 class="page-title">ログイン</h2>

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

    <form method="POST" action="{{ route('login') }}" class="form" novalidate>
        @csrf

    <div class="form_group">
        <label class="form-label">メールアドレス</label>
        <input type="email" name="email" class="form_control" value="{{ old('email') }}" required>
        @error('email') <p class="error">{{ $message }}</p> @enderror
    </div>
    <div class="form_group">
        <label class="form_label">パスワード</label>
        <input type="password" name="password" class="form_control" required>
        @error('password') <p class="error">{{ $message }}</p> @enderror
    </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">ログイン</button>
    </form>
    <div class="link-wrapper">
    <a href="{{ route('register') }}" class="link-like">会員登録はこちら</a>
</div>
</div>
@endsection
