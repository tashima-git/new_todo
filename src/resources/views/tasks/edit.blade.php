@extends('layouts.app')

@section('title', 'タスク編集')

@section('css')
<link rel="stylesheet" href="/css/task-create.css">
@endsection

@section('content')
    @php
        $statusValue = $task->status->value;
        $categoryValue = $task->category->value;
    @endphp

    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <h1 class="tk-page__title">タスク編集</h1>
            </div>

            <div class="tk-page__actions tk-row tk-row--gap">
                <a href="{{ route('tasks.index', ['status' => $statusValue]) }}" class="tk-btn tk-btn--ghost">
                    一覧へ
                </a>
            </div>
        </div>

        {{-- 編集フォーム --}}
        <div class="tk-card">
            <form method="POST" action="{{ route('tasks.update', $task) }}" class="tk-form">
                @csrf
                @method('PUT')

                {{-- タイトル --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="title">タスク名</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        class="tk-input"
                        value="{{ old('title', $task->title) }}"
                        required
                        maxlength="255"
                        autocomplete="off"
                    >
                </div>

                {{-- メモ --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="memo">メモ・内容</label>
                    <textarea
                        id="memo"
                        name="memo"
                        class="tk-input tk-textarea"
                        maxlength="2000"
                        rows="4"
                        placeholder="必要な手順、補足、完了条件などを自由に書けます"
                    >{{ old('memo', $task->memo) }}</textarea>
                    <div class="tk-help">
                        ※ 任意入力です。タスク一覧にも短く表示されます
                    </div>
                </div>

                <div class="tk-form-grid tk-form-grid--meta">
                    {{-- カテゴリ --}}
                    <div class="tk-form__group">
                        <label class="tk-label" for="category">カテゴリ</label>
                        <select id="category" name="category" class="tk-select" required>
                            <option value="work" @selected(old('category', $categoryValue) === 'work')>仕事・学校</option>
                            <option value="private" @selected(old('category', $categoryValue) === 'private')>プライベート</option>
                        </select>
                    </div>

                    {{-- 期限 --}}
                    <div class="tk-form__group">
                        <label class="tk-label" for="due_date">期限</label>
                        <div class="tk-date-field">
                            <input
                                id="due_date"
                                name="due_date"
                                type="date"
                                min="{{ now()->toDateString() }}"
                                class="tk-input tk-date-input"
                                value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                            >
                            <button type="button" class="tk-date-button" data-date-picker-target="due_date">
                                日付を選ぶ
                            </button>
                        </div>
                    </div>

                    {{-- 重要度 --}}
                    <div class="tk-form__group">
                        <label class="tk-label" for="importance">重要度</label>
                        <select id="importance" name="importance" class="tk-select" required>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected((int)old('importance', $task->importance) === $i)>
                                    {{ $i }}（{{ ['低', 'やや低', '普通', '高', '最重要'][$i - 1] }}）
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- 緊急 --}}
                    <div class="tk-form__group">
                        <label class="tk-label">緊急</label>
                        <label class="tk-check">
                            <input type="hidden" name="is_urgent" value="0">
                            <input
                                type="checkbox"
                                name="is_urgent"
                                value="1"
                                @checked((bool)old('is_urgent', $task->is_urgent))
                            >
                            <span>急ぎ（緊急）</span>
                        </label>
                    </div>
                </div>

                {{-- ステータス割り振り --}}
                <hr class="tk-hr">

                <div class="tk-section">
                    <div class="tk-section__title">
                        <div class="tk-section__en">Stats</div>
                        <div class="tk-section__ja">ステータス割り振り</div>
                    </div>

                    <div class="tk-help">
                        MVPでは合計0〜6ポイントの範囲で割り振ります。全て0でも登録できます。
                    </div>

                    <div class="tk-grid tk-grid--2">
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_patience">忍耐</label>
                            <input
                                id="stat_patience"
                                name="stat_patience"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_patience', $task->stat_patience) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>

                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_speed">迅速</label>
                            <input
                                id="stat_speed"
                                name="stat_speed"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_speed', $task->stat_speed) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>

                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_focus">集中</label>
                            <input
                                id="stat_focus"
                                name="stat_focus"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_focus', $task->stat_focus) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>

                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_accuracy">正確</label>
                            <input
                                id="stat_accuracy"
                                name="stat_accuracy"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_accuracy', $task->stat_accuracy) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>

                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_life">生活力</label>
                            <input
                                id="stat_life"
                                name="stat_life"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_life', $task->stat_life) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>

                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_strategy">戦略</label>
                            <input
                                id="stat_strategy"
                                name="stat_strategy"
                                type="number"
                                class="tk-input"
                                value="{{ old('stat_strategy', $task->stat_strategy) }}"
                                min="0"
                                max="5"
                                required
                            >
                        </div>
                    </div>
                </div>

                {{-- 保存 --}}
                <hr class="tk-hr">

                <div class="tk-row tk-row--gap tk-row--right">
                    <a href="{{ route('tasks.index', ['status' => $statusValue]) }}" class="tk-btn tk-btn--ghost">
                        キャンセル
                    </a>

                    <button type="submit" class="tk-btn tk-btn--primary">
                        更新する
                    </button>
                </div>
            </form>

            @if ($statusValue === 'pending')
                <div class="tk-delete-inline" style="display: flex; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="tk-btn tk-btn--ghost" id="openDeleteTaskModal">
                        削除
                    </button>

                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" id="deleteTaskForm">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endif
        </div>

        @if ($statusValue === 'pending')
            <div class="tk-confirm-modal" id="deleteTaskModal" aria-hidden="true" hidden style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center;">
                <div class="tk-confirm-modal__backdrop" data-delete-modal-cancel style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.72);"></div>
                <div class="tk-confirm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="deleteTaskModalTitle" style="position: relative; width: min(92vw, 440px); padding: 22px; border: 2px solid var(--text-main); border-radius: 10px; background: var(--bg-sub); color: var(--text-main); box-shadow: 0 18px 50px rgba(0, 0, 0, 0.45);">
                    <div class="tk-confirm-modal__title" id="deleteTaskModalTitle">
                        タスクを削除しますか？
                    </div>
                    <div class="tk-confirm-modal__body">
                        この操作は取り消せません。未完了タスクのみ削除できます。
                    </div>
                    <div class="tk-confirm-modal__actions">
                        <button type="button" class="tk-confirm-modal__cancel" data-delete-modal-cancel>
                            取消
                        </button>
                        <button type="button" class="tk-confirm-modal__delete" id="confirmDeleteTask">
                            削除
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('scripts')
<script>
(() => {
    document.querySelectorAll('[data-date-picker-target]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.datePickerTarget);
            if (!input) return;

            if (typeof input.showPicker === 'function') {
                input.showPicker();
                return;
            }

            input.focus();
        });
    });
})();
</script>

@if ($statusValue === 'pending')
<script>
(() => {
    const modal = document.getElementById('deleteTaskModal');
    const openButton = document.getElementById('openDeleteTaskModal');
    const confirmButton = document.getElementById('confirmDeleteTask');
    const deleteForm = document.getElementById('deleteTaskForm');

    if (!modal || !openButton || !confirmButton || !deleteForm) return;

    function openModal() {
        modal.hidden = false;
        modal.style.display = 'flex';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        modal.hidden = true;
    }

    openButton.addEventListener('click', openModal);
    confirmButton.addEventListener('click', () => deleteForm.submit());

    document.addEventListener('click', event => {
        if (!event.target.closest('[data-delete-modal-cancel]')) return;
        closeModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        closeModal();
    });
})();
</script>
@endif
@endsection
