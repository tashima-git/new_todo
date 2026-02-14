@extends('layouts.app')

@section('title', '戦歴')

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
        'mob' => ['ja' => '雑魚', 'en' => 'Mob'],
        'mid' => ['ja' => '中ボス', 'en' => 'Mid Boss'],
        'boss' => ['ja' => '大ボス', 'en' => 'Boss'],
    ];
@endphp

<div class="space-y-6">

    {{-- タイトル --}}
    <section class="space-y-1">
        <h1 class="text-2xl font-bold">戦歴</h1>
        <p class="text-sm text-gray-600">
            討伐結果の記録です。タスクの詳細（ステ振り等）は保存しません。
        </p>
    </section>

    {{-- サマリー --}}
    <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded border bg-white p-4">
            <div class="text-xs text-gray-500">Total</div>
            <div class="text-lg font-bold">総討伐数</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($summary['total_kills'] ?? 0) }}
            </div>
        </div>

        <div class="rounded border bg-white p-4">
            <div class="text-xs text-gray-500">Mob</div>
            <div class="text-lg font-bold">雑魚</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($summary['mob'] ?? 0) }}
            </div>
        </div>

        <div class="rounded border bg-white p-4">
            <div class="text-xs text-gray-500">Mid Boss</div>
            <div class="text-lg font-bold">中ボス</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($summary['mid'] ?? 0) }}
            </div>
        </div>

        <div class="rounded border bg-white p-4">
            <div class="text-xs text-gray-500">Boss</div>
            <div class="text-lg font-bold">大ボス</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($summary['boss'] ?? 0) }}
            </div>
        </div>
    </section>

    {{-- 最近の討伐 --}}
    <section class="rounded border bg-white">
        <div class="border-b px-4 py-3 flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Recent</div>
                <div class="font-bold">最近の討伐</div>
            </div>

            <div class="text-xs text-gray-500">
                ※最新20件まで表示
            </div>
        </div>

        <div class="p-4">
            @if ($recentLogs->isEmpty())
                <div class="text-sm text-gray-600">
                    まだ討伐ログがありません。<br>
                    <a href="{{ route('tasks.index') }}" class="underline">タスクを完了</a>して、
                    <a href="{{ route('taskkill.index') }}" class="underline">TaskKill</a>を実行すると戦歴が残ります。
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-3">種別</th>
                                <th class="py-2 pr-3">討伐対象</th>
                                <th class="py-2 pr-3">作成日</th>
                                <th class="py-2 pr-3">完了日</th>
                                <th class="py-2 pr-3 text-right">獲得</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($recentLogs as $log)
                                @php
                                    $type = $log->boss_type ?? 'mob';
                                    $typeJa = $bossTypeLabel[$type]['ja'] ?? $type;
                                    $typeEn = $bossTypeLabel[$type]['en'] ?? $type;

                                    $gainedTotal =
                                        ($log->gained_patience ?? 0) +
                                        ($log->gained_speed ?? 0) +
                                        ($log->gained_focus ?? 0) +
                                        ($log->gained_accuracy ?? 0) +
                                        ($log->gained_life ?? 0) +
                                        ($log->gained_strategy ?? 0);
                                @endphp

                                <tr class="border-b last:border-b-0">
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-500">{{ $typeEn }}</div>
                                        <div class="font-semibold">{{ $typeJa }}</div>
                                    </td>

                                    <td class="py-3 pr-3 min-w-[240px]">
                                        <div class="font-semibold">
                                            {{ $log->task_title ?? '（タイトル不明）' }}
                                        </div>
                                    </td>

                                    <td class="py-3 pr-3 whitespace-nowrap text-gray-700">
                                        {{ optional($log->task_created_at)->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td class="py-3 pr-3 whitespace-nowrap text-gray-700">
                                        {{ optional($log->task_completed_at)->format('Y-m-d') ?? '-' }}
                                    </td>

                                    <td class="py-3 pr-0 whitespace-nowrap text-right">
                                        <div class="font-bold">
                                            +{{ number_format($gainedTotal) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ステ合計
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    {{-- 次の導線 --}}
    <section class="rounded border bg-white p-4">
        <div class="space-y-2">
            <div class="font-bold">次にできること</div>

            <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                <li>
                    <a href="{{ route('status.index') }}" class="underline">
                        ステータスを見る
                    </a>
                </li>
                <li>
                    <a href="{{ route('taskkill.index') }}" class="underline">
                        討伐待ちを一気に処理する
                    </a>
                </li>
            </ul>
        </div>
    </section>

</div>
@endsection
