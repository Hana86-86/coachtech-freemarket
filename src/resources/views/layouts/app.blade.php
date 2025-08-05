<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '商品一覧')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <h1>Freemarket</h1>
        <nav>
            <a href="{{ route('items.index') }}">商品一覧</a>
            @guest
            <a href="{{ route('register') }}">会員登録</a>
            <a href="{{ route('login') }}">ログイン</a>
        @endguest

        @auth
            <a href="#">マイページ</a>
            <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            ログアウト
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
            </form>
        @endauth

            <!-- 検索フォーム -->
            <form action="{{ route('items.index') }}" method="GET" style="display:inline;">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="何をお探しですか？">
                <button type="submit">検索</button>
            </form>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Freemarket</p>
    </footer>
</body>
</html>