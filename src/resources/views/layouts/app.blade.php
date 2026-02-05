<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Todo App</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <head>
        <div class="head-content">
            <h1 class="title">Todo</h1>
            <nav class="nav">
                <ul>
                    @if (Auth::check() && Auth::user()->role === 'users')
                        @auth
                            <li><a href="{{ route('users.logouts') }}">ログアウト</a></li>
                        @else
                            <li><a href="{{ route('users.login') }}">ログイン</a></li>
                        @endauth
                    @elseif (Auth::check() && Auth::user()->role === 'admins')
                        @auth
                            <li><a href="{{ route('admins.logouts') }}">ログアウト</a></li>
                        @else
                            <li><a href="{{ route('admins.login') }}">ログイン</a></li>
                        @endauth
                </ul>
        </div>
    </head>

    <body>
        <div class="content">
            @yield('content')
        </div>
    </body>
</html>




