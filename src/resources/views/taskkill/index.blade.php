@extends('layouts.app')

@section('title', 'TaskKill')

@section('content')
<div class="tk-page">

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

    <div class="tk-card">
        <div class="tk-help">
            完了タスクを順番に討伐します。
        </div>
    </div>

    @if ($tasks->isEmpty())

        <div class="tk-card">
            <div class="tk-empty">
                討伐待ちのタスクがありません
            </div>
        </div>

    @else

        <div class="tk-card">
            <div id="taskkill-root"
                data-tasks='@json($tasks->values())'
                data-execute-url="{{ route('taskkill.execute') }}">
            </div>
        </div>

    @endif

</div>
@endsection