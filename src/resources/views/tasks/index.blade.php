@extends('layouts.app')

@section('title', 'タスク一覧')

@section('css')
<link rel="stylesheet" href="/css/tasks.css">
@endsection

@section('content')
<div class="tasks-container">

    {{-- ===============================
        ヘッダー
    =============================== --}}
    <div class="tasks-header">
        <div class="tasks-title">
            <h1>タスク</h1>
        </div>
        <div>
            <a href="{{ route('tasks.create') }}" class="btn-create">+ タスク作成</a>
        </div>
    </div>

    <!-- 開発用タスク生成ボタン -->
    <form method="POST" action="/dev/generate">
        @csrf
        <button type="submit">テストタスク生成</button>
    </form>

    {{-- ===============================
        タブ
    =============================== --}}
    <div class="tasks-tabs">
        <a href="{{ route('tasks.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
           class="tab {{ request('status', 'pending') === 'pending' ? 'active' : '' }}">
            未完了
        </a>

        <a href="{{ route('tasks.index', array_merge(request()->except('page'), ['status' => 'stocked'])) }}"
           class="tab {{ request('status') === 'stocked' ? 'active' : '' }}">
            完了（討伐待ち）
        </a>
    </div>

    {{-- ===============================
        フィルタ
    =============================== --}}
    <div class="tasks-filter">
        <form method="GET" action="{{ route('tasks.index') }}" class="filter-form">

            <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
            <input type="hidden" name="view" value="{{ request('view', 'tree') }}">

            <div class="filter-item">
                <label>カテゴリ</label>
                <select name="category">
                    <option value="">すべて</option>
                    <option value="work" {{ request('category') === 'work' ? 'selected' : '' }}>仕事・学校</option>
                    <option value="private" {{ request('category') === 'private' ? 'selected' : '' }}>プライベート</option>
                </select>
            </div>

            <div class="filter-item">
                <label>ボス種別</label>
                <select name="boss_type">
                    <option value="">すべて</option>
                    <option value="mob" {{ request('boss_type') === 'mob' ? 'selected' : '' }}>雑魚</option>
                    <option value="mid" {{ request('boss_type') === 'mid' ? 'selected' : '' }}>中ボス</option>
                    <option value="boss" {{ request('boss_type') === 'boss' ? 'selected' : '' }}>大ボス</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">絞り込み</button>
                <a href="{{ route('tasks.index', [
                    'status' => request('status', 'pending'),
                    'view' => request('view', 'tree')
                ]) }}"
                class="btn-reset">
                    リセット
                </a>
            </div>
        </form>
    </div>

        {{-- ===============================
        一括操作
    =============================== --}}
    <form method="POST" action="{{ route('tasks.bulk') }}" id="bulkForm">
        @csrf
        <input type="hidden" name="current_status" value="{{ request('status', 'pending') }}">

        <div class="bulk-actions-wrapper">
            <div class="bulk-controls">
                <select name="action" id="bulkAction">
                    <option value="">一括操作を選択</option>
                    <option value="complete">完了</option>
                    <option value="uncomplete">未完了に戻す</option>
                    <option value="delete">削除</option>
                </select>
                <button type="submit" id="bulkSubmit" disabled>実行</button>
            </div>
        </div>

    {{-- ===============================
        表示モード切替
    =============================== --}}
    <div class="view-tabs">

        <a href="{{ route('tasks.index', array_merge(request()->all(), ['view' => 'tree'])) }}"
           class="view-tab {{ request('view', 'tree') === 'tree' ? 'active' : '' }}">
            ツリー表示
        </a>

        <a href="{{ route('tasks.index', array_merge(request()->all(), ['view' => 'flat'])) }}"
           class="view-tab {{ request('view') === 'flat' ? 'active' : '' }}">
            一覧（期限順）
        </a>

    </div>


        {{-- ===============================
            テーブル
        =============================== --}}
        <div class="tasks-table-wrapper">
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>タスク</th>
                        <th>カテゴリ</th>
                        <th>期限</th>
                        <th>重要度</th>
                        <th>緊急</th>
                        <th>ボス</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>

@forelse ($tasks as $task)

    {{-- ===============================
        フラット表示
    =============================== --}}
    @if(request('view') === 'flat')

        @include('tasks.partials.task-row', [
            'task' => $task,
            'level' => 0,
            'parentId' => null
        ])

    {{-- ===============================
        ツリー表示
    =============================== --}}
    @else

        @if($status === 'pending')
            @if(is_null($task->parent_task_id))
                @include('tasks.partials.task-row', [
                    'task' => $task,
                    'level' => 0,
                    'parentId' => null
                ])
            @endif
        @else
            @include('tasks.partials.task-row', [
                'task' => $task,
                'level' => 0,
                'parentId' => null
            ])
        @endif

    @endif

@empty
                    <tr>
                        <td colspan="8" class="text-center">タスクがありません</td>
                    </tr>
@endforelse

                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $tasks->links() }}
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/tasks-index.js') }}"></script>
@endpush