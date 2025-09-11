@extends('layouts.app')

@section('content')
<div class="container container--narrow">
    <div class="verify__inner">
    <p class="verify__lead">
    登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してくだい。
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="flash flash--success" role="status">認証メールを送信しました！</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-primary">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="link-like verify_resend">認証メールを再送信</button>
</form>

</div>
</div>
@endsection