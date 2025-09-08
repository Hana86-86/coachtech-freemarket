@extends('layouts.app')

@section('content')
<div class="container container--narrow">
    <div class="verify__inner">

    <p class="verify__lead">
    登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。</p>

    <div class="mt-4">
    <button type="button" class="btn btn-secondary" disabled>
        認証はこちらから
    </button>
</div>

    @if (session('status') == 'verification-link-sent')
        <div class="flash flash--success" role="status">新しい認証メールを送信しました！</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="link-like verify_resend">認証メールを再送信</button>
</form>
</div>
</div>
@endsection