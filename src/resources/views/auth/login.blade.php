@extends('layouts.app')

@section('content')
    <h2>ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <button type="submit">ログイン</button>
        </div>
    </form>

    <div>
        <a href="{{ route('register') }}">新規登録はこちら</a>
    </div>

@endsection