@extends('layouts.app')

@section('title', '成長')

@section('css')
<link rel="stylesheet" href="/css/chapters.css">
@endsection

@section('content')
@php
    $statFields = array_keys($statLabels);
    $lifetimeStats = [
        'total_patience' => $user->total_patience ?? 0,
        'total_speed' => $user->total_speed ?? 0,
        'total_focus' => $user->total_focus ?? 0,
        'total_accuracy' => $user->total_accuracy ?? 0,
        'total_life' => $user->total_life ?? 0,
        'total_strategy' => $user->total_strategy ?? 0,
    ];
@endphp

<div class="chapter-page">
    <section class="chapter-header">
        <div>
            <div class="chapter-kicker">Growth</div>
            <h1>成長</h1>
            <p>旅の目的とステータスをまとめて確認します。今どこへ向かい、どれだけ成長したかを見る場所です。</p>
        </div>
    </section>

    @if (!$activeChapter)
        <section class="chapter-card chapter-card--wide">
            <div class="chapter-card__header">
                <div>
                    <h2>新しい旅を始める</h2>
                    <p>期限ではなく、今向かっている方向を短く書いてください。</p>
                </div>
                <span class="chapter-badge">Start</span>
            </div>

            <form method="POST" action="{{ route('chapters.store') }}" class="chapter-form">
                @csrf
                <label class="chapter-field">
                    <span>旅の目的</span>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="例：エンジニアとして自立する"
                        maxlength="80"
                        required
                    >
                </label>
                <button type="submit" class="chapter-button">旅を始める</button>
            </form>
        </section>
    @else
        <section class="chapter-card chapter-card--wide chapter-current">
            <div class="chapter-card__header">
                <div>
                    <h2>現在の旅</h2>
                    <p>この旅の中でどれだけ成長したかを記録します。</p>
                </div>
                <span class="chapter-badge">Active</span>
            </div>

            <div class="chapter-title-panel">
                <div class="chapter-title-label">旅の目的</div>
                <div class="chapter-title">{{ $activeChapter->title }}</div>
                <div class="chapter-days">
                    開始 {{ optional($activeChapter->started_at)->format('Y/m/d') }}
                    /
                    {{ max(1, (int) $activeChapter->started_at->diffInDays(now()) + 1) }}日目
                </div>
            </div>

            <div class="chapter-stat-grid">
                @foreach ($statLabels as $field => $label)
                    <div class="chapter-stat">
                        <span>{{ $label }}</span>
                        <strong>{{ number_format($activeChapter->{$field} ?? 0) }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="chapter-grid">
            <section class="chapter-card">
                <div class="chapter-card__header">
                    <div>
                        <h2>生涯累積</h2>
                        <p>過去の旅で得た成長も失われません。</p>
                    </div>
                    <span class="chapter-badge">Total</span>
                </div>

                <div class="chapter-mini-stat-grid">
                    @foreach ($statLabels as $field => $label)
                        <div class="chapter-mini-stat">
                            <span>{{ $label }}</span>
                            <strong>{{ number_format($lifetimeStats[$field] ?? 0) }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="chapter-card">
                <div class="chapter-card__header">
                    <div>
                        <h2>旅を終える</h2>
                        <p>現在の旅の合計を残し、新しい旅を0から始めます。</p>
                    </div>
                    <span class="chapter-badge">Next</span>
                </div>

                <form method="POST" action="{{ route('chapters.finish') }}" class="chapter-form">
                    @csrf
                    <label class="chapter-field">
                        <span>次の旅の目的</span>
                        <input
                            type="text"
                            name="next_title"
                            value="{{ old('next_title') }}"
                            placeholder="例：個人開発で収益を作る"
                            maxlength="80"
                            required
                        >
                    </label>
                    <button type="submit" class="chapter-button chapter-button--ghost">
                        現在の旅を終えて次へ
                    </button>
                </form>
            </section>
        </div>
    @endif

    <section class="chapter-card chapter-card--wide">
        <div class="chapter-card__header">
            <div>
                <h2>過去の旅</h2>
                <p>詳細ログではなく、旅ごとのステータス合計だけを残します。</p>
            </div>
            <span class="chapter-badge">History</span>
        </div>

        @if ($pastChapters->isEmpty())
            <div class="chapter-empty">まだ終えた旅はありません。</div>
        @else
            <div class="chapter-history">
                @foreach ($pastChapters as $chapter)
                    <div class="chapter-history-row">
                        <div>
                            <strong>{{ $chapter->title }}</strong>
                            <span>
                                {{ optional($chapter->started_at)->format('Y/m/d') }}
                                -
                                {{ optional($chapter->ended_at)->format('Y/m/d') }}
                            </span>
                        </div>
                        <div class="chapter-history-stats">
                            @foreach ($statLabels as $field => $label)
                                <span>{{ $label }} {{ number_format($chapter->{$field} ?? 0) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
