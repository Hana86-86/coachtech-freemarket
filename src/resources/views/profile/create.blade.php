@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<div class="form-container">
    <h2 class="form-title">プロフィール設定</h2>

    {{-- 成功メッセージ --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.store') }}">
        @csrf
        <label for="phone">ユーザー名</label>
        <input type="text" name="phone" value="{{ old('name') }}" required>

        <label for="postal_code">郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required>

        <label for="address">住所</label>
        <input type="text" name="address" value="{{ old('address') }}" required>

        <label for="building">建物名（任意）</label>
        <input type="text" name="building" value="{{ old('building') }}">

        <input type="file" name="profile_image">
        
        <button type="submit">登録する</button>
    </form>
</div>
@endsection