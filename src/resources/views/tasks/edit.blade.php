@extends('layouts.app')

@section('title', 'タスク編集')

@section('content')
    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <div class="tk-page__en">Edit</div>
                <h1 class="tk-page__title">タスク編集</h1>
            </div>

            <div class="tk-page__actions tk-row tk-row--gap">
                <a href="{{ route('tasks.show', $task) }}" class="tk-btn tk-btn--ghost">
                    ← 詳細へ戻る
                </a>
                <a href="{{ route('tasks.index', ['status' => $task->status]) }}" class="tk-btn tk-btn--ghost">
                    一覧へ
                </a>
            </div>
        </div>

        {{-- 注意（討伐待ち） --}}
        @if ($task->status === 'stocked')
            <div class="tk-alert tk-alert--warning">
                <div class="tk-alert__title">このタスクは「討伐待ち」です</div>
                <div class="tk-alert__text">
                    内容は編集できますが、完了日時は「完了にした瞬間」に更新されます。
                </div>
            </div>
        @endif

        {{-- 編集フォーム --}}
        <div class="tk-card">
            <form method="POST" action="{{ route('tasks.update', $task) }}" class="tk-form">
                @csrf
                @method('PUT')

                {{-- タイトル --}}
                <div class="tk-field">
                    <label class="tk-label" for="title">タイトル</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        class="tk-input"
                        value="{{ old('title', $task->title) }}"
                        required
                        maxlength="100"
                        autocomplete="off"
                    >
                    <div class="tk-help">100文字まで</div>
                </div>

                {{-- 期限 --}}
                <div class="tk-field">
                    <label class="tk-label" for="due_date">期限</label>
                    <input
                        id="due_date"
                        name="due_date"
                        type="date"
                        class="tk-input"
                        value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                    >
                    <div class="tk-help">完了しても期限は保持されます（TaskKillまで残す）</div>
                </div>

                {{-- 重要度 --}}
                <div class="tk-field">
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
                <div class="tk-field">
                    <label class="tk-label">緊急</label>
                    <label class="tk-check">
                        <input
                            type="checkbox"
                            name="is_urgent"
                            value="1"
                            @checked((bool)old('is_urgent', $task->is_urgent))
                        >
                        <span>急ぎ（緊急）</span>
                    </label>
                </div>

                {{-- カテゴリ --}}
                <div class="tk-field">
                    <label class="tk-label" for="category">カテゴリ</label>
                    <select id="category" name="category" class="tk-select" required>
                        <option value="work" @selected(old('category', $task->category) === 'work')>仕事・学校</option>
                        <option value="private" @selected(old('category', $task->category) === 'private')>プライベート</option>
                    </select>
                </div>

                {{-- 難度 --}}
                <div class="tk-field">
                    <label class="tk-label" for="difficulty">難度</label>
                    <select id="difficulty" name="difficulty" class="tk-select" required>
                        <option value="easy" @selected(old('difficulty', $task->difficulty) === 'easy')>雑魚（Easy）</option>
                        <option value="normal" @selected(old('difficulty', $task->difficulty) === 'normal')>中ボス（Normal）</option>
                        <option value="hard" @selected(old('difficulty', $task->difficulty) === 'hard')>大ボス（Hard）</option>
                    </select>
                    <div class="tk-help">TaskKillでの演出・統計に影響します</div>
                </div>

                {{-- 親タスク --}}
                <div class="tk-field">
                    <label class="tk-label" for="parent_task_id">親タスクID（任意）</label>
                    <input
                        id="parent_task_id"
                        name="parent_task_id"
                        type="number"
                        class="tk-input"
                        value="{{ old('parent_task_id', $task->parent_task_id) }}"
                        min="1"
                    >
                    <div class="tk-help">
                        大ボスを分割したい時に使う（後でUIで選択式にもできる）
                    </div>
                </div>

                {{-- ステータス割り振り --}}
                <div class="tk-divider"></div>

                <div class="tk-section">
                    <div class="tk-section__title">
                        <div class="tk-section__en">Stats</div>
                        <div class="tk-section__ja">ステータス割り振り</div>
                    </div>

                    <div class="tk-grid tk-grid--2">
                        <div class="tk-field">
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

                        <div class="tk-field">
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

                        <div class="tk-field">
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

                        <div class="tk-field">
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

                        <div class="tk-field">
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

                        <div class="tk-field">
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

                    <div class="tk-help">
                        ここは「合計がいくつまで」みたいな制限も付けられる（次の段階で）
                    </div>
                </div>

                {{-- 保存 --}}
                <div class="tk-divider"></div>

                <div class="tk-row tk-row--gap tk-row--right">
                    <a href="{{ route('tasks.show', $task) }}" class="tk-btn tk-btn--ghost">
                        キャンセル
                    </a>

                    <button type="submit" class="tk-btn tk-btn--primary">
                        更新する
                    </button>
                </div>
            </form>
        </div>

        {{-- 削除（未完了のみ） --}}
        @if ($task->status === 'pending')
            <div class="tk-card">
                <div class="tk-section">
                    <div class="tk-section__title">
                        <div class="tk-section__en">Danger</div>
                        <div class="tk-section__ja">削除</div>
                    </div>

                    <div class="tk-help">
                        未完了タスクのみ削除できます。完了タスクは履歴として残します。
                    </div>

                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                        onsubmit="return confirm('本当に削除しますか？この操作は取り消せません。')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="tk-btn tk-btn--danger">
                            このタスクを削除する
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
@endsection
