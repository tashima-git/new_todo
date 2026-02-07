<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Todo App</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>


<body>
        <header class="header">
            <h1 class="title">
                <a href="{{ url('/') }}">Todo</a>
            </h1>

            <nav class="nav">
                <ul>
                    @if (!Auth::check())
                        <li><a href="{{ route('login') }}">ログイン</a></li>
                        <li><a href="{{ route('admins.login') }}">管理者ログイン</a></li>
                    @endif

                    @if (Auth::check() && Auth::user()->role === 'admins')
                            <li><a href="{{ route('admins.logout') }}">ログアウト</a></li>
                    @elseif (Auth::check() && Auth::user()->role === 'users')
                            <li><a href="{{ route('users.logout') }}">ログアウト</a></li>
                    @endif
                </ul>
            </nav>
        </header>


        <div class="content">
            @yield('content')
        </div>

</body>
</html>




