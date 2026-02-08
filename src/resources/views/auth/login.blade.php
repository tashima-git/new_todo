@extends('layouts.app')

@section('css')
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
    <h2>ログイン</h2>

    <div class="auth-container">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-content">
            <label for="email" class="auth-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="auth-content">
            <label for="password" class="auth-label">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <button type="submit" class="auth-button">ログイン</button>
        </div>
    </form>
    </div>

    <div>
        <a href="{{ route('register') }}">新規登録はこちら</a>
    </div>

@endsection