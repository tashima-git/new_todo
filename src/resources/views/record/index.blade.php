@extends('layouts.app')

@section('title', '戦歴')

@section('css')
<link rel="stylesheet" href="/css/record.css">
@endsection

@section('content')
@php
    $summary = $summary ?? [
        'total_kills' => 0,
        'mob' => 0,
        'mid' => 0,
        'boss' => 0,
    ];

    $recentLogs = $recentLogs ?? collect();

    $bossTypeLabel = [
        'mob' => ['ja' => '雑魚'],
        'mid' => ['ja' => '中ボス'],
        'boss' => ['ja' => '大ボス'],
    ];
@endphp

<div class="record">

    {{-- タイトル --}}
    <section class="record-header">
        <h1>戦歴</h1>
        <p>
            討伐結果の記録です。タスクの詳細（ステ振り等）は保存しません。
        </p>
    </section>

    {{-- サマリー --}}
    <section class="record-summary">

        <div class="record-card">
            <div class="record-label">総討伐数</div>
            <div class="record-value">
                {{ number_format($summary['total_kills'] ?? 0) }}
            </div>
        </div>

        <div class="record-card">
            <div class="record-label">雑魚</div>
            <div class="record-value">
                {{ number_format($summary['mob'] ?? 0) }}
            </div>
        </div>

        <div class="record-card">
            <div class="record-label">中ボス</div>
            <div class="record-value">
                {{ number_format($summary['mid'] ?? 0) }}
            </div>
        </div>

        <div class="record-card">
            <div class="record-label">大ボス</div>
            <div class="record-value">
                {{ number_format($summary['boss'] ?? 0) }}
            </div>
        </div>

    </section>

    {{-- 最近の討伐 --}}
    <section class="record-table">

        <div class="record-table-header">
            <div>最近の討伐</div>
            <div class="note">※最新20件まで表示</div>
        </div>

        <div class="record-table-body">

            @if ($recentLogs->isEmpty())
                <div class="record-empty">
                    まだ討伐ログがありません。
                </div>
            @else

                <table>
                    <thead>
                        <tr>
                            <th>種別</th>
                            <th>討伐対象</th>
                            <th>作成日</th>
                            <th>完了日</th>
                            <th class="right">ステ合計</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($recentLogs as $log)
                            @php
                                $type = $log->boss_type ?? 'mob';
                                $typeJa = $bossTypeLabel[$type]['ja'] ?? $type;

                                $gainedTotal =
                                    ($log->gained_patience ?? 0) +
                                    ($log->gained_speed ?? 0) +
                                    ($log->gained_focus ?? 0) +
                                    ($log->gained_accuracy ?? 0) +
                                    ($log->gained_life ?? 0) +
                                    ($log->gained_strategy ?? 0);
                            @endphp

                            <tr>
                                <td class="nowrap">
                                    <strong>{{ $typeJa }}</strong>
                                </td>

                                <td class="title">
                                    {{ $log->task_title ?? '（タイトル不明）' }}
                                </td>

                                <td class="nowrap">
                                    {{ optional($log->task_created_at)->format('Y-m-d') ?? '-' }}
                                </td>

                                <td class="nowrap">
                                    {{ optional($log->task_completed_at)->format('Y-m-d') ?? '-' }}
                                </td>

                                <td class="right">
                                    <strong>+{{ number_format($gainedTotal) }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif

        </div>

    </section>

</div>
@endsection