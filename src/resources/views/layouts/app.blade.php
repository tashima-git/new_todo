<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Todo App</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>


<body>
        <header class="header">
            <h1 class="title">
                @if (Auth::guard('admin')->check())
                    <a href="{{ url('/admin') }}">メンバー一覧</a>
                @else
                    <a href="{{ url('/') }}">Todo</a>
                @endif
            </h1>

            <nav class="nav">
                <ul>
                    @if (Auth::guard('admin')->check())
                        <li><form method="POST" action="{{ route('admins.logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">管理者ログアウト</button>
                        </form></li>
                    @elseif (Auth::guard('web')->check())
                        <li><form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">ログアウト</button>
                        </form></li>
                    @else
                        <li><a href="{{ route('login') }}">ログイン</a></li>
                        <li><a href="{{ route('admins.login') }}">管理者ログイン</a></li>
                    @endif
                </ul>
            </nav>
        </header>


        <div class="content">
            @yield('content')
        </div>

</body>
</html>




