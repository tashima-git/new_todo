@extends('layouts.app')

@section('title', 'TaskKill')

@section('content')
    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <div class="tk-page__en">TaskKill</div>
                <h1 class="tk-page__title">討伐待ち</h1>
            </div>

            <div class="tk-page__actions tk-row tk-row--gap">
                <a href="{{ route('tasks.index', ['status' => 'stocked']) }}" class="tk-btn tk-btn--ghost">
                    討伐待ちを編集
                </a>
                <a href="{{ route('tasks.index') }}" class="tk-btn tk-btn--ghost">
                    タスク一覧へ
                </a>
            </div>
        </div>

        {{-- 説明 --}}
        <div class="tk-card">
            <div class="tk-help">
                ここには「完了（討伐待ち）」になったタスクが並びます。<br>
                討伐すると結果が記録され、ステータスに反映されます。
            </div>
        </div>

        {{-- 討伐待ちがない場合 --}}
        @if ($tasks->isEmpty())
            <div class="tk-card">
                <div class="tk-empty">
                    <div class="tk-empty__title">討伐待ちのタスクがありません</div>
                    <div class="tk-empty__text">
                        タスク一覧で完了にすると、ここに移動します。
                    </div>

                    <div class="tk-row tk-row--gap">
                        <a href="{{ route('tasks.index') }}" class="tk-btn tk-btn--primary">
                            タスク一覧へ
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- 討伐フォーム --}}
            <form method="GET" action="{{ route('taskkill.result') }}" id="tkKillForm">

                <div class="tk-card">
                    <div class="tk-row tk-row--between tk-row--gap">
                        <div>
                            <div class="tk-section__title">
                                <div class="tk-section__en">Targets</div>
                                <div class="tk-section__ja">討伐対象</div>
                            </div>
                            <div class="tk-help">
                                選択したタスクをまとめて討伐します。
                            </div>
                        </div>

                        <div class="tk-row tk-row--gap">
                            <button type="button" class="tk-btn tk-btn--ghost" id="btnSelectAll">
                                全選択
                            </button>
                            <button type="button" class="tk-btn tk-btn--ghost" id="btnClearAll">
                                全解除
                            </button>

                            <form method="POST" action="{{ route('taskkill.execute') }}">
                                @csrf
                                <button type="submit">討伐開始</button>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- 一覧 --}}
                <div class="tk-card">
                    <div class="tk-table-wrap">
                        <table class="tk-table">
                            <thead>
                                <tr>
                                    <th style="width: 44px;">選択</th>
                                    <th>タスク</th>
                                    <th style="width: 110px;">タイプ</th>
                                    <th style="width: 120px;">期限</th>
                                    <th style="width: 90px;">重要度</th>
                                    <th style="width: 90px;">緊急</th>
                                    <th style="width: 170px;">完了日時</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($tasks as $task)
                                    @php
                                        // 表示用（Controller側で boss_type を付けて渡す想定）
                                        $bossType = $task->boss_type ?? null;

                                        $bossLabel = match ($bossType) {
                                            'boss' => '大ボス',
                                            'mid' => '中ボス',
                                            'mob' => '雑魚',
                                            default => '判定中',
                                        };

                                        $dueLabel = $task->due_date
                                            ? $task->due_date->format('Y-m-d')
                                            : 'なし';

                                        $urgentLabel = $task->is_urgent ? '緊急' : '通常';
                                    @endphp

                                    <tr>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="task_ids[]"
                                                value="{{ $task->id }}"
                                                class="tk-checkbox tk-task-check"
                                            >
                                        </td>

                                        <td>
                                            <div class="tk-task">
                                                <div class="tk-task__title">
                                                    <a href="{{ route('tasks.show', $task) }}" class="tk-link">
                                                        {{ $task->title }}
                                                    </a>
                                                </div>

                                                <div class="tk-task__meta">
                                                    <span class="tk-badge">
                                                        {{ $task->category === 'work' ? '仕事・学校' : 'プライベート' }}
                                                    </span>

                                                    @if ($task->parent_task_id)
                                                        <span class="tk-badge tk-badge--sub">
                                                            親: #{{ $task->parent_task_id }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="tk-badge
                                                @if ($bossType === 'boss') tk-badge--boss
                                                @elseif ($bossType === 'mid') tk-badge--mid
                                                @elseif ($bossType === 'mob') tk-badge--mob
                                                @endif
                                            ">
                                                {{ $bossLabel }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="tk-text">
                                                {{ $dueLabel }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="tk-badge">
                                                {{ $task->importance }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="tk-badge @if($task->is_urgent) tk-badge--danger @endif">
                                                {{ $urgentLabel }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="tk-text">
                                                {{ $task->completed_at ? $task->completed_at->format('Y-m-d H:i') : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tk-help">
                        ※ 討伐後は「討伐結果」に移動し、ステータス反映が行われます。
                    </div>
                </div>

            </form>
        @endif

    </div>
@endsection

@section('js')
<script>
    (function () {
        const checks = Array.from(document.querySelectorAll('.tk-task-check'));
        const btnSelectAll = document.getElementById('btnSelectAll');
        const btnClearAll = document.getElementById('btnClearAll');
        const btnKill = document.getElementById('btnKill');
        const form = document.getElementById('tkKillForm');

        function updateKillButton() {
            const selectedCount = checks.filter(c => c.checked).length;
            btnKill.disabled = selectedCount === 0;
            btnKill.textContent = selectedCount === 0
                ? '討伐する'
                : `討伐する（${selectedCount}件）`;
        }

        if (btnSelectAll) {
            btnSelectAll.addEventListener('click', function () {
                checks.forEach(c => c.checked = true);
                updateKillButton();
            });
        }

        if (btnClearAll) {
            btnClearAll.addEventListener('click', function () {
                checks.forEach(c => c.checked = false);
                updateKillButton();
            });
        }

        checks.forEach(c => c.addEventListener('change', updateKillButton));

        if (form) {
            form.addEventListener('submit', function (e) {
                const selectedCount = checks.filter(c => c.checked).length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('討伐するタスクを選択してください。');
                    return;
                }
            });
        }

        updateKillButton();
    })();
</script>
@endsection
