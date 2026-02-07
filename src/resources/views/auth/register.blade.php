@extends('layouts.app')

@section('content')
    <h2>新規登録</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>

        <div>
            <button type="submit">登録</button>
        </div>
    </form>

    <div>
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
@endsection