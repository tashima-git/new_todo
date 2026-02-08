@extends('layouts.app')

@section('css')
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
    <h2>新規登録</h2>

    <div class="auth-container">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-content">
            <label for="name" class="auth-label">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="auth-content">
            <label for="email" class="auth-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="auth-content">
            <label for="password" class="auth-label">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="auth-content">
            <label for="password_confirmation" class="auth-label">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <div>
            <button type="submit" class="auth-button">登録</button>
        </div>
    </form>
    </div>

    <div>
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
@endsection