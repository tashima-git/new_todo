@extends('layouts.app')

@section('content')
    <h2>管理者ログイン</h2>
    <form method="POST" action="{{ route('admins.login') }}">
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

@endsection