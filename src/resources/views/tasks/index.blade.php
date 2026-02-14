@extends('layouts.app')

@section('title', 'タスク一覧')

@section('content')
    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <div class="tk-page__en">Task</div>
                <h1 class="tk-page__title">タスク一覧</h1>
            </div>

            <div class="tk-page__actions">
                <a href="{{ route('tasks.create') }}" class="tk-btn tk-btn--primary">
                    + タスク作成
                </a>
            </div>
        </div>

        {{-- フィルタ（最低限） --}}
        <div class="tk-card">
            <form method="GET" action="{{ route('tasks.index') }}" class="tk-form tk-form--inline">

                {{-- 状態 --}}
                <div class="tk-form__group">
                    <label class="tk-label">表示</label>
                    <select name="status" class="tk-select">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>
                            未完了（pending）
                        </option>
                        <option value="stocked" {{ request('status') === 'stocked' ? 'selected' : '' }}>
                            完了（討伐待ち / stocked）
                        </option>
                    </select>
                </div>

                {{-- カテゴリ --}}
                <div class="tk-form__group">
                    <label class="tk-label">カテゴリ</label>
                    <select name="category" class="tk-select">
                        <option value="">すべて</option>
                        <option value="work" {{ request('category') === 'work' ? 'selected' : '' }}>
                            仕事・学校
                        </option>
                        <option value="private" {{ request('category') === 'private' ? 'selected' : '' }}>
                            プライベート
                        </option>
                    </select>
                </div>

                {{-- ソート --}}
                <div class="tk-form__group">
                    <label class="tk-label">並び順</label>
                    <select name="sort" class="tk-select">
                        <option value="due_date" {{ request('sort', 'due_date') === 'due_date' ? 'selected' : '' }}>
                            期限が近い順
                        </option>
                        <option value="importance" {{ request('sort') === 'importance' ? 'selected' : '' }}>
                            重要度が高い順
                        </option>
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>
                            新しい順
                        </option>
                    </select>
                </div>

                <div class="tk-form__group">
                    <button type="submit" class="tk-btn tk-btn--sub">
                        絞り込み
                    </button>

                    <a href="{{ route('tasks.index') }}" class="tk-btn tk-btn--ghost">
                        リセット
                    </a>
                </div>
            </form>
        </div>

        {{-- 一括操作 --}}
        <div class="tk-card">
            <form method="POST" action="{{ route('tasks.bulk') }}" id="bulkForm">
                @csrf

                {{-- このhiddenで「いま一覧がどっち表示か」を送る（混在バグ防止） --}}
                <input type="hidden" name="current_status" value="{{ request('status', 'pending') }}">

                <div class="tk-bulk">
                    <div class="tk-bulk__left">
                        <div class="tk-bulk__title">
                            <div class="tk-bulk__ja">一括操作</div>
                        </div>

                        <div class="tk-bulk__hint">
                            ※ 未完了と完了（討伐待ち）は混ぜて選択できません
                        </div>
                    </div>

                    <div class="tk-bulk__right">
                        <select name="action" class="tk-select" required>
                            <option value="">操作を選択</option>

                            {{-- pending表示時 --}}
                            @if (request('status', 'pending') === 'pending')
                                <option value="complete">選択を完了（討伐待ちへ）</option>
                                <option value="delete">選択を削除（pendingのみ）</option>
                            @endif

                            {{-- stocked表示時 --}}
                            @if (request('status') === 'stocked')
                                <option value="uncomplete">選択を未完了に戻す</option>
                            @endif
                        </select>

                        <button type="submit" class="tk-btn tk-btn--primary" id="bulkSubmit" disabled>
                            実行
                        </button>
                    </div>
                </div>

                {{-- タスク一覧テーブル --}}
                <div class="tk-table-wrap">
                    <table class="tk-table">
                        <thead>
                            <tr>
                                <th class="tk-col-check">
                                    <input type="checkbox" id="checkAll">
                                </th>

                                <th>タスク</th>
                                <th class="tk-col-category">カテゴリ</th>
                                <th class="tk-col-due">期限</th>
                                <th class="tk-col-importance">重要度</th>
                                <th class="tk-col-urgent">緊急</th>
                                <th class="tk-col-actions">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td class="tk-col-check">
                                        <input type="checkbox" name="task_ids[]" value="{{ $task->id }}"
                                            class="task-check">
                                    </td>

                                    <td class="tk-col-title">
                                        <div class="tk-task-title">
                                            <a href="{{ route('tasks.show', $task) }}" class="tk-link">
                                                {{ $task->title }}
                                            </a>
                                        </div>

                                        {{-- stockedの場合だけ完了日時を出す --}}
                                        @if ($task->status === 'stocked' && $task->completed_at)
                                            <div class="tk-task-sub">
                                                完了: {{ \Carbon\Carbon::parse($task->completed_at)->format('Y/m/d H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="tk-col-category">
                                        @if ($task->category === 'work')
                                            <span class="tk-badge">仕事・学校</span>
                                        @else
                                            <span class="tk-badge">プライベート</span>
                                        @endif
                                    </td>

                                    <td class="tk-col-due">
                                        @if ($task->due_date)
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}
                                        @else
                                            <span class="tk-muted">なし</span>
                                        @endif
                                    </td>

                                    <td class="tk-col-importance">
                                        <span class="tk-importance">
                                            {{ $task->importance }}
                                        </span>
                                    </td>

                                    <td class="tk-col-urgent">
                                        @if ($task->is_urgent)
                                            <span class="tk-badge tk-badge--danger">急ぎ</span>
                                        @else
                                            <span class="tk-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="tk-col-actions">
                                        <div class="tk-actions">
                                            <a href="{{ route('tasks.edit', $task) }}" class="tk-btn tk-btn--sub">
                                                編集
                                            </a>

                                            {{-- 個別操作 --}}
                                            @if ($task->status === 'pending')
                                                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="tk-btn tk-btn--primary">
                                                        完了
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                                    onsubmit="return confirm('このタスクを削除しますか？（未完了のみ削除可能）')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tk-btn tk-btn--danger">
                                                        削除
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($task->status === 'stocked')
                                                <form method="POST" action="{{ route('tasks.uncomplete', $task) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="tk-btn tk-btn--sub">
                                                        戻す
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="tk-empty">
                                        タスクがありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        {{-- ページネーション --}}
        <div class="tk-pagination">
            {{ $tasks->links() }}
        </div>

        {{-- TaskKill導線（stocked表示の時だけ） --}}
        @if (request('status') === 'stocked')
            <div class="tk-card">
                <div class="tk-callout">
                    <div class="tk-callout__left">
                        <div class="tk-callout__en">Ready</div>
                        <div class="tk-callout__ja">討伐待ちタスクが溜まっています</div>
                    </div>
                    <div class="tk-callout__right">
                        <a href="{{ route('taskkill.index') }}" class="tk-btn tk-btn--primary">
                            TaskKillへ
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('js')
    <script>
        (function() {
            const checkAll = document.getElementById('checkAll');
            const checks = document.querySelectorAll('.task-check');
            const bulkSubmit = document.getElementById('bulkSubmit');

            function updateBulkButton() {
                const anyChecked = Array.from(checks).some(ch => ch.checked);
                bulkSubmit.disabled = !anyChecked;
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checks.forEach(ch => ch.checked = checkAll.checked);
                    updateBulkButton();
                });
            }

            checks.forEach(ch => {
                ch.addEventListener('change', function() {
                    // 全部チェックされていたら checkAll もONにする
                    if (checkAll) {
                        checkAll.checked = Array.from(checks).every(x => x.checked);
                    }
                    updateBulkButton();
                });
            });

            updateBulkButton();
        })();
    </script>
@endsection
