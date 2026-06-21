@extends('layouts.app')

@section('title', 'メールアドレス変更')

@section('css')
<link rel="stylesheet" href="/css/settings.css">
@endsection

@section('content')
@php
    $user = $user ?? auth()->user();
@endphp

<div class="settings-page settings-page--narrow">
    <section class="settings-header">
        <div>
            <div class="settings-kicker">Account</div>
            <h1>メールアドレス変更</h1>
            <p>認証に関わる情報なので、設定トップとは分けて扱います。</p>
        </div>
    </section>

    <section class="settings-card">
        <div class="settings-card__header">
            <div>
                <h2>メールアドレス</h2>
                <p>現在はUI確認用です。保存処理は未実装です。</p>
            </div>
            <span class="settings-badge">Email</span>
        </div>

        <div class="settings-rows">
            <label class="settings-field">
                <span>現在のメールアドレス</span>
                <input type="email" value="{{ $user->email ?? '' }}" disabled>
            </label>

            <label class="settings-field">
                <span>新しいメールアドレス</span>
                <input type="email" placeholder="new-mail@example.com">
            </label>

            <label class="settings-field">
                <span>現在のパスワード</span>
                <input type="password" placeholder="本人確認のため入力">
            </label>
        </div>
    </section>

    <section class="settings-actions">
        <a href="{{ route('settings.index') }}" class="settings-link-button settings-link-button--ghost">設定へ戻る</a>
        <button type="button">変更する</button>
    </section>
</div>
@endsection
