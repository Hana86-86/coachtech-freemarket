<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>@yield('title','Freemarket')</title>

  {{-- 共通CSS --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  @stack('styles')
</head>
<body>

  {{-- ヘッダー：未ログイン＝ロゴのみ / ログイン後＝検索＋ナビ --}}
  <header class="site-header">
    <div class="site-header__inner">
      {{-- ブランドロゴ（常に表示） --}}
      <a href="{{ route('products.index') }}" class="site-header__brand">
        <img src="{{ asset('storage/common/logo.svg') }}" alt="COACHTECH" style="height:22px">
      </a>
  {{-- 未ログイン（ゲスト用）--}}
      @guest
      <div class="nav__links">
      <a href="{{ route('register') }}">会員登録</a>
      <a href="{{ route('login') }}">ログイン</a>
      </div>
      @endguest

      {{-- ログイン後（認証済みユーザー）用 --}}
    @auth
      <form action="{{ route('products.index') }}" method="GET" class="nav__search" role="search">
        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="何をお探しですか？" />
        <button type="submit">検索</button>
      </form>

      <div class="nav__links">
        <a href="{{ route('mypage.index') }}">マイページ</a>
        <a href="{{ route('products.create') }}">出品</a>
        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">ログアウト</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
          @csrf
        </form>
      </div>
    @endauth
  </div>
</header>

  <main class="container">
    @yield('content')
  </main>

  <footer class="footer">
    <div class="container">
      <p>© {{ now()->year }} Freemarket</p>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>