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

    @if (Auth::check())
    <link rel="stylesheet" href="{{ asset('css/header-logged-in.css') }}">
    @else
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    @endif
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo" width="200">
            </a>

            @if (!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('verification.notice'))
                @if (Auth::check())
                <!-- ログイン後のヘッダー -->
                <div class="header__search">
                    <form action="{{ route('items.index') }}" method="GET">
                        <input type="text" name="search" placeholder="なにをお探しですか？" value="{{ request('search') }}">
                        <button type="submit">検索</button>
                    </form>
                </div>

                <nav class="header-nav">
                    <a href="/mypage" class="header-nav__link">マイページ</a>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="header-nav__button">ログアウト</button>
                    </form>
                    <a href="/sell" class="header__sell-button">出品</a>
                </nav>
                @endif
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
