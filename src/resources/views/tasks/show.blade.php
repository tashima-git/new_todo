@extends('layouts.app')

@section('title', 'タスク詳細')

@section('content')
    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <div class="tk-page__en">Detail</div>
                <h1 class="tk-page__title">タスク詳細</h1>
            </div>

            <div class="tk-page__actions tk-row tk-row--gap">
                <a href="{{ route('tasks.index', ['status' => $task->status]) }}" class="tk-btn tk-btn--ghost">
                    ← 一覧へ戻る
                </a>

                <a href="{{ route('tasks.edit', $task) }}" class="tk-btn tk-btn--sub">
                    編集
                </a>
            </div>
        </div>

        {{-- タスク概要 --}}
        <div class="tk-card">
            <div class="tk-task-detail">

                <div class="tk-task-detail__title">
                    {{ $task->title }}
                </div>

                <div class="tk-task-detail__meta tk-grid tk-grid--2">

                    {{-- 状態 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">状態</div>
                        <div class="tk-meta__value">
                            @if ($task->status === 'pending')
                                <span class="tk-badge">未完了</span>
                            @elseif ($task->status === 'stocked')
                                <span class="tk-badge tk-badge--warning">討伐待ち</span>
                            @else
                                <span class="tk-badge tk-badge--sub">討伐済み</span>
                            @endif
                        </div>
                    </div>

                    {{-- カテゴリ --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">カテゴリ</div>
                        <div class="tk-meta__value">
                            @if ($task->category === 'work')
                                <span class="tk-badge">仕事・学校</span>
                            @else
                                <span class="tk-badge">プライベート</span>
                            @endif
                        </div>
                    </div>

                    {{-- 期限 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">期限</div>
                        <div class="tk-meta__value">
                            @if ($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}
                            @else
                                <span class="tk-muted">なし</span>
                            @endif
                        </div>
                    </div>

                    {{-- 重要度 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">重要度</div>
                        <div class="tk-meta__value">
                            <span class="tk-importance">
                                {{ $task->importance }}
                            </span>
                            <span class="tk-muted">
                                （1=低 / 5=最重要）
                            </span>
                        </div>
                    </div>

                    {{-- 緊急 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">緊急</div>
                        <div class="tk-meta__value">
                            @if ($task->is_urgent)
                                <span class="tk-badge tk-badge--danger">急ぎ</span>
                            @else
                                <span class="tk-muted">なし</span>
                            @endif
                        </div>
                    </div>

                    {{-- 完了日時（stockedの時だけ意味がある） --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">完了日時</div>
                        <div class="tk-meta__value">
                            @if ($task->completed_at)
                                {{ \Carbon\Carbon::parse($task->completed_at)->format('Y/m/d H:i') }}
                            @else
                                <span class="tk-muted">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- 作成日時 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">作成</div>
                        <div class="tk-meta__value">
                            {{ $task->created_at ? $task->created_at->format('Y/m/d H:i') : '-' }}
                        </div>
                    </div>

                    {{-- 更新日時 --}}
                    <div class="tk-meta">
                        <div class="tk-meta__label">更新</div>
                        <div class="tk-meta__value">
                            {{ $task->updated_at ? $task->updated_at->format('Y/m/d H:i') : '-' }}
                        </div>
                    </div>
                </div>

                {{-- 個別操作 --}}
                <div class="tk-row tk-row--gap tk-row--right">
                    @if ($task->status === 'pending')
                        <form method="POST" action="{{ route('tasks.complete', $task) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="tk-btn tk-btn--primary">
                                完了（討伐待ちへ）
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
                                未完了に戻す
                            </button>
                        </form>

                        <a href="{{ route('taskkill.index') }}" class="tk-btn tk-btn--primary">
                            TaskKillへ
                        </a>
                    @endif

                    @if ($task->status === 'killed')
                        <a href="{{ route('stats.index') }}" class="tk-btn tk-btn--sub">
                            戦績を見る
                        </a>
                    @endif
                </div>

            </div>
        </div>

        {{-- ステータス割り振り --}}
        <div class="tk-card">
            <div class="tk-section">
                <div class="tk-section__title">
                    <div class="tk-section__en">Stats</div>
                    <div class="tk-section__ja">ステータス割り振り</div>
                </div>

                <div class="tk-grid tk-grid--2">
                    <div class="tk-stat">
                        <div class="tk-stat__name">忍耐</div>
                        <div class="tk-stat__value">{{ $task->stat_patience }}</div>
                        <div class="tk-stat__desc">筋トレ、有酸素運動など</div>
                    </div>

                    <div class="tk-stat">
                        <div class="tk-stat__name">迅速</div>
                        <div class="tk-stat__value">{{ $task->stat_speed }}</div>
                        <div class="tk-stat__desc">返信、報告、素早い処理</div>
                    </div>

                    <div class="tk-stat">
                        <div class="tk-stat__name">集中</div>
                        <div class="tk-stat__value">{{ $task->stat_focus }}</div>
                        <div class="tk-stat__desc">勉強、会議、集中作業</div>
                    </div>

                    <div class="tk-stat">
                        <div class="tk-stat__name">正確</div>
                        <div class="tk-stat__value">{{ $task->stat_accuracy }}</div>
                        <div class="tk-stat__desc">資料作成、設計、連絡</div>
                    </div>

                    <div class="tk-stat">
                        <div class="tk-stat__name">生活力</div>
                        <div class="tk-stat__value">{{ $task->stat_life }}</div>
                        <div class="tk-stat__desc">買い物、家事、生活タスク</div>
                    </div>

                    <div class="tk-stat">
                        <div class="tk-stat__name">戦略</div>
                        <div class="tk-stat__value">{{ $task->stat_strategy }}</div>
                        <div class="tk-stat__desc">準備、整備、段取り</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ボスツリー（親） --}}
        <div class="tk-card">
            <div class="tk-section">
                <div class="tk-section__title">
                    <div class="tk-section__en">Tree</div>
                    <div class="tk-section__ja">ボスツリー</div>
                </div>

                @if ($task->parent_task_id)
                    <div class="tk-help">
                        このタスクは親タスクに紐づいています
                    </div>

                    <div class="tk-row tk-row--gap">
                        <a href="{{ route('tasks.show', $task->parent_task_id) }}" class="tk-btn tk-btn--sub">
                            親タスクを見る
                        </a>
                    </div>
                @else
                    <div class="tk-muted">
                        親タスクはありません
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
