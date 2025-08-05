@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2>メール認証が必要です</h2>
    <p>ご登録のメールアドレスに確認リンクを送信しました。<br>
       メールのリンクをクリックして認証を完了してください。</p>

    @if (session('status') == 'verification-link-sent')
        <div style="color:green;">新しい認証メールを送信しました！</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">認証メールを再送信</button>
    </form>
</div>
@endsection