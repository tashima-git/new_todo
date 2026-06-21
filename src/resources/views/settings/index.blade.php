@extends('layouts.app')

@section('title', '設定')

@section('css')
<link rel="stylesheet" href="/css/settings.css">
@endsection

@section('content')
@php
    $user = $user ?? auth()->user();
    $settingValues = array_replace($settings ?? [], old('settings', []));
    $userName = old('name', $user->name ?? '');
@endphp

<form class="settings-page" method="POST" action="{{ route('settings.update') }}">
    @csrf

    <section class="settings-header">
        <div>
            <div class="settings-kicker">Config</div>
            <h1>設定</h1>
            <p>音、表示、アカウントまわりの調整画面です。</p>
        </div>
        <div class="settings-status">保存すると次回表示にも反映されます</div>
    </section>

    <div class="settings-grid">
        <section class="settings-card settings-card--wide">
            <div class="settings-card__header">
                <div>
                    <h2>アカウント</h2>
                    <p>表示名はここで変更し、認証情報は専用画面で扱います。</p>
                </div>
                <span class="settings-badge">Account</span>
            </div>

            <div class="settings-account-grid">
                <label class="settings-field settings-account-name">
                    <span>表示名</span>
                    <input type="text" name="name" value="{{ $userName }}" placeholder="表示名" required maxlength="20">
                </label>

                <div class="settings-account-name-action">
                    <button type="submit">表示名を変更</button>
                </div>

                <div class="settings-account-summary">
                    <span>現在のメールアドレス</span>
                    <strong>{{ $user->email ?? '未設定' }}</strong>
                </div>

                <div class="settings-account-actions">
                    <a href="{{ route('settings.email') }}" class="settings-link-button">メールアドレス変更へ</a>
                    <a href="{{ route('settings.password') }}" class="settings-link-button">パスワード変更へ</a>
                </div>
            </div>
        </section>

        <section class="settings-card settings-card--wide">
            <div class="settings-card__header">
                <div>
                    <h2>音</h2>
                    <p>音量を0にすると、その系統の音は鳴らない想定です。</p>
                </div>
                <span class="settings-badge">Audio</span>
            </div>

            <div class="settings-rows">
                <label class="settings-field">
                    <span>SE音量（共通）</span>
                    <div class="settings-range">
                        <input type="range" name="settings[se_volume]" min="0" max="100" value="{{ $settingValues['se_volume'] }}">
                        <output>{{ $settingValues['se_volume'] }}%</output>
                    </div>
                    <small>ページ移動や通常クリックなど、基本操作の効果音です。</small>
                </label>

                <label class="settings-field">
                    <span>討伐時SE音量</span>
                    <div class="settings-range">
                        <input type="range" name="settings[taskkill_se_volume]" min="0" max="100" value="{{ $settingValues['taskkill_se_volume'] }}">
                        <output>{{ $settingValues['taskkill_se_volume'] }}%</output>
                    </div>
                    <small>TaskKillの攻撃音、撃破音、結果演出の効果音です。</small>
                </label>

                <label class="settings-field">
                    <span>ステータスSE音量</span>
                    <div class="settings-range">
                        <input type="range" name="settings[status_se_volume]" min="0" max="100" value="{{ $settingValues['status_se_volume'] }}">
                        <output>{{ $settingValues['status_se_volume'] }}%</output>
                    </div>
                    <small>ステータス画面や成長演出で鳴る効果音です。</small>
                </label>

                <div class="settings-field">
                    <span>ボイス種類</span>
                    <div class="settings-choice-grid">
                        <label><input type="radio" name="settings[voice_type]" value="none" @checked($settingValues['voice_type'] === 'none')> なし</label>
                        <label><input type="radio" name="settings[voice_type]" value="guide" @checked($settingValues['voice_type'] === 'guide')> 案内人</label>
                        <label><input type="radio" name="settings[voice_type]" value="samurai" @checked($settingValues['voice_type'] === 'samurai')> 武者</label>
                        <label><input type="radio" name="settings[voice_type]" value="partner" @checked($settingValues['voice_type'] === 'partner')> 静かな相棒</label>
                    </div>
                </div>

                <label class="settings-field">
                    <span>ボイス音量</span>
                    <div class="settings-range">
                        <input type="range" name="settings[voice_volume]" min="0" max="100" value="{{ $settingValues['voice_volume'] }}">
                        <output>{{ $settingValues['voice_volume'] }}%</output>
                    </div>
                </label>
            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card__header">
                <div>
                    <h2>表示・操作</h2>
                    <p>演出の強さや一覧の初期表示を調整します。</p>
                </div>
                <span class="settings-badge">View</span>
            </div>

            <div class="settings-rows">
                <label class="settings-field">
                    <span>初期表示</span>
                    <select name="settings[default_task_view]">
                        <option value="tree" @selected($settingValues['default_task_view'] === 'tree')>ツリー表示</option>
                        <option value="flat" @selected($settingValues['default_task_view'] === 'flat')>一覧（期限順）</option>
                    </select>
                </label>

                <label class="settings-row">
                    <span>
                        <strong>重要操作の確認を増やす</strong>
                        <small>削除や一括操作の誤操作を防ぎます</small>
                    </span>
                    <input type="hidden" name="settings[confirm_important_actions]" value="0">
                    <input type="checkbox" name="settings[confirm_important_actions]" value="1" @checked($settingValues['confirm_important_actions'])>
                </label>
            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card__header">
                <div>
                    <h2>通知</h2>
                    <p>期限や未討伐タスクの気づきを増やします。</p>
                </div>
                <span class="settings-badge">Notice</span>
            </div>

            <div class="settings-rows">
                <label class="settings-row">
                    <span>
                        <strong>期限前の通知</strong>
                        <small>期限が近いタスクを知らせる想定</small>
                    </span>
                    <input type="hidden" name="settings[deadline_notification_enabled]" value="0">
                    <input type="checkbox" name="settings[deadline_notification_enabled]" value="1" @checked($settingValues['deadline_notification_enabled'])>
                </label>

                <label class="settings-field">
                    <span>通知タイミング</span>
                    <select name="settings[deadline_notification_timing]">
                        <option value="same_day" @selected($settingValues['deadline_notification_timing'] === 'same_day')>当日</option>
                        <option value="one_day_before" @selected($settingValues['deadline_notification_timing'] === 'one_day_before')>1日前</option>
                        <option value="three_days_before" @selected($settingValues['deadline_notification_timing'] === 'three_days_before')>3日前</option>
                    </select>
                </label>

            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card__header">
                <div>
                    <h2>その他</h2>
                    <p>今後あると便利そうな調整項目です。</p>
                </div>
                <span class="settings-badge">Extra</span>
            </div>

            <div class="settings-rows">
                <label class="settings-field">
                    <span>1ページのタスク表示数</span>
                    <select name="settings[tasks_per_page]">
                        <option value="10" @selected((int) $settingValues['tasks_per_page'] === 10)>10件</option>
                        <option value="20" @selected((int) $settingValues['tasks_per_page'] === 20)>20件</option>
                        <option value="50" @selected((int) $settingValues['tasks_per_page'] === 50)>50件</option>
                    </select>
                </label>

                <label class="settings-field">
                    <span class="settings-label-with-note">
                        タスク作成時自動戦略加算
                        <span class="settings-scroll-note" tabindex="0" aria-label="補足">
                            <span class="settings-note-tooltip" role="tooltip">
                                タスクを作る行為も戦略として評価するため、作成時に自動で加算する想定です。
                            </span>
                        </span>
                    </span>
                    <select name="settings[auto_strategy_on_create]">
                        <option value="0" @selected((int) $settingValues['auto_strategy_on_create'] === 0)>+0</option>
                        <option value="1" @selected((int) $settingValues['auto_strategy_on_create'] === 1)>+1</option>
                        <option value="2" @selected((int) $settingValues['auto_strategy_on_create'] === 2)>+2</option>
                        <option value="3" @selected((int) $settingValues['auto_strategy_on_create'] === 3)>+3</option>
                    </select>
                </label>

            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card__header">
                <div>
                    <h2>データ管理</h2>
                    <p>バックアップや整理の導線です。</p>
                </div>
                <span class="settings-badge">Data</span>
            </div>

            <div class="settings-button-stack">
                <button type="button">設定を初期化する</button>
                <button type="button" class="settings-button--danger">アカウント削除</button>
            </div>
        </section>
    </div>

    <section class="settings-actions">
        <button type="button" class="settings-button--ghost" id="settingsDiscardButton">変更を破棄</button>
        <button type="submit" id="settingsSaveButton" disabled>設定を保存</button>
    </section>
</form>

<div class="settings-confirm-modal" id="settingsLeaveModal" aria-hidden="true" hidden>
    <div class="settings-confirm-modal__backdrop" data-settings-leave-cancel></div>
    <div class="settings-confirm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="settingsLeaveModalTitle">
        <div class="settings-confirm-modal__title" id="settingsLeaveModalTitle">
            変更を破棄しますか？
        </div>
        <div class="settings-confirm-modal__body">
            保存していない設定があります。このまま移動すると変更内容は破棄されます。
        </div>
        <div class="settings-confirm-modal__actions">
            <button type="button" class="settings-button--ghost" data-settings-leave-cancel>
                キャンセル
            </button>
            <button type="button" id="settingsLeaveConfirmButton">
                破棄して移動
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(() => {
    const page = document.querySelector('.settings-page');
    const saveButton = document.getElementById('settingsSaveButton');
    const discardButton = document.getElementById('settingsDiscardButton');
    const modal = document.getElementById('settingsLeaveModal');
    const leaveConfirmButton = document.getElementById('settingsLeaveConfirmButton');

    if (!page || !saveButton || !discardButton || !modal || !leaveConfirmButton) return;

    const controls = Array.from(page.querySelectorAll('input, select'));
    let initialValues = controls.map(controlValue);
    let pendingUrl = null;

    function controlValue(control) {
        if (control.type === 'checkbox' || control.type === 'radio') {
            return control.checked ? '1' : '0';
        }

        return control.value;
    }

    function isDirty() {
        return controls.some((control, index) => controlValue(control) !== initialValues[index]);
    }

    function syncRangeOutput(control) {
        if (control.type !== 'range') return;

        const output = control.closest('.settings-range')?.querySelector('output');
        if (!output) return;

        output.textContent = `${control.value}%`;
    }

    function refreshDirtyState() {
        saveButton.disabled = !isDirty();
    }

    function resetControls() {
        controls.forEach((control, index) => {
            if (control.type === 'checkbox' || control.type === 'radio') {
                control.checked = initialValues[index] === '1';
            } else {
                control.value = initialValues[index];
            }

            syncRangeOutput(control);
        });

        refreshDirtyState();
    }

    function openLeaveModal(url) {
        pendingUrl = url;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeLeaveModal() {
        pendingUrl = null;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
    }

    controls.forEach(control => {
        syncRangeOutput(control);
        control.addEventListener('input', () => {
            syncRangeOutput(control);
            refreshDirtyState();
        });
        control.addEventListener('change', refreshDirtyState);
    });

    discardButton.addEventListener('click', resetControls);

    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || !isDirty()) return;

        const url = link.href;
        if (!url || url === window.location.href) return;

        event.preventDefault();
        event.stopPropagation();
        openLeaveModal(url);
    }, true);

    document.addEventListener('click', event => {
        if (!event.target.closest('[data-settings-leave-cancel]')) return;
        closeLeaveModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape' || modal.hidden) return;
        closeLeaveModal();
    });

    leaveConfirmButton.addEventListener('click', () => {
        if (!pendingUrl) return;
        window.location.href = pendingUrl;
    });

    refreshDirtyState();
})();
</script>
@endsection
