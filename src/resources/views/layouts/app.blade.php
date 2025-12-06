<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Freemarket')</title>

  {{-- 共通CSS --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  @stack('styles')
</head>

<body class="fm">

  {{-- ヘッダー --}}
  @php
  $isTradeChatPage = request()->routeIs('trades.chat.*');
  @endphp

  <header class="site-header">
    <div class="container site-header__inner">

      {{-- ブランドロゴ（常に表示） --}}
      <a href="{{ route('products.index') }}" class="site-header__brand">
        <img src="{{ asset('storage/common/logo.svg') }}" height="22" alt="COACHTECH">
      </a>
      @if(! $isTradeChatPage)
      {{-- 検索フォーム --}}
      <form action="{{ route('products.index') }}" method="GET" class="nav_search" role="search">
        <input type="text" name="keyword" class="nav_search_input"
          placeholder="何をお探しですか？"
          value="{{ request('keyword') }}">

        <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">

      </form>

      {{-- 右端スペーサー（フレックスの余白） --}}
      <div class="site-header__spacer"></div>

      {{-- 右端リンク --}}
      <ul class="nav_links">
        @auth
        <li>
          <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            ログアウト
          </a>
        </li>
        <li><a href="{{ route('profile.show') }}">マイページ</a></li>
        <li><a href="{{ route('products.create') }}" class="nav_link nav_link--sell">出品</a></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
          @csrf
        </form>
        @else
        <li><a href="{{ route('login') }}">ログイン</a></li>
        <li><a href="{{ route('profile.show') }}">マイページ</a></li>
        <li><a href="{{ route('products.create') }}" class="nav_link nav_link--sell">出品</a></li>
        @endauth
      </ul>
      @endif
    </div> {{-- /.site-header__inner --}}
  </header>

  <main class="container">
    @if (session('success'))
    <div class="flash flash--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="flash flash--error">{{ session('error') }}</div>
    @endif
    @if (session('status'))
    <div class="flash flash--info">{{ session('status') }}</div>
    @endif

    @yield('content')
  </main>

  {{--
  <footer class="footer">
    <div class="container">
      <p>© {{ now()->year }} Freemarket</p>
  </div>
  </footer>
  --}}


  @stack('scripts')
</body>

</html>