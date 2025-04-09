<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('css')
</head>

<body>
    <header class="header">
    <div class="header__inner">
        <!-- ロゴ（常に表示） -->
        <a class="header__logo" href="/">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo" width="200">
        </a>

        {{-- 検索欄：ログイン済み または 商品一覧・商品詳細ページの時のみ --}}
        @if (Auth::check() || request()->routeIs('items.index') || request()->routeIs('items.show'))
            <div class="header__search">
                <form action="{{ route('items.index') }}" method="GET">
                    <input type="text" name="search" placeholder="なにをお探しですか？" value="{{ request('search') }}">
                    <button type="submit">検索</button>
                </form>
            </div>
        @endif

        {{-- ナビゲーション：ログイン済み または 商品一覧・商品詳細ページの時のみ --}}
        @if (Auth::check() || request()->routeIs('items.index') || request()->routeIs('items.show'))
            <nav class="header-nav">
                @if (Auth::check())
                    <a href="/mypage" class="header-nav__link">マイページ</a>
                    <form action="/logout" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="header-nav__button">ログアウト</button>
                    </form>
                @else
                    <a href="/login" class="header-nav__link">ログイン</a>
                    <a href="/mypage" class="header-nav__link">マイページ</a>
                @endif
                <a href="/sell" class="header__sell-button">出品</a>
            </nav>
        @endif
    </div>
</header>

    <div class="container">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>
</body>

</html>
