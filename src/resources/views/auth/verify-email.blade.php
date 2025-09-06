@extends('layouts.app')

@section('content')
<div class="container container--narrow">
    <div class="verify__inner">

    <p class="verify__lead">
    登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。</p>

    @php
        $user = auth()->user();
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(,)
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    @endphp

    <a href="{{ $verifyUrl }}" class="btn btn--ghost verify__cta">認証はこちらから</a>

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