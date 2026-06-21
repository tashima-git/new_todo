@extends('layouts.app')

@section('title', 'パスワード変更')

@section('css')
<link rel="stylesheet" href="/css/settings.css">
@endsection

@section('content')
<div class="settings-page settings-page--narrow">
    <section class="settings-header">
        <div>
            <div class="settings-kicker">Account</div>
            <h1>パスワード変更</h1>
            <p>重要な変更なので、専用画面で確認しながら進めます。</p>
        </div>
    </section>

    <section class="settings-card">
        <div class="settings-card__header">
            <div>
                <h2>パスワード</h2>
                <p>現在はUI確認用です。保存処理は未実装です。</p>
            </div>
            <span class="settings-badge">Password</span>
        </div>

        <div class="settings-rows">
            <label class="settings-field">
                <span>現在のパスワード</span>
                <input type="password" placeholder="現在のパスワード">
            </label>

            <label class="settings-field">
                <span>新しいパスワード</span>
                <input type="password" placeholder="8文字以上">
            </label>

            <label class="settings-field">
                <span>新しいパスワード（確認）</span>
                <input type="password" placeholder="もう一度入力">
            </label>
        </div>
    </section>

    <section class="settings-actions">
        <a href="{{ route('settings.index') }}" class="settings-link-button settings-link-button--ghost">設定へ戻る</a>
        <button type="button">変更する</button>
    </section>
</div>
@endsection
