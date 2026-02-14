@extends('layouts.app')

@section('title', '実績')

@section('content')
@php
    // Controllerから渡される想定：
    // $achievements = Achievement::with('userAchievements')->... の一覧
    // ただし未実装でも表示が崩れないように保険
    $achievements = $achievements ?? collect();

    $unlockedCount = $unlockedCount ?? 0;
    $totalCount = $totalCount ?? $achievements->count();

    // condition_type の表示名（必要になったら増やせる）
    $conditionTypeLabel = [
        'total_stat' => '総ステータス',
        'task_completed' => '討伐数',
        'streak_days' => '連続達成',
    ];
@endphp

<div class="space-y-6">

    {{-- タイトル --}}
    <section class="space-y-1">
        <h1 class="text-2xl font-bold">実績</h1>
        <p class="text-sm text-gray-600">
            達成した証がここに残ります。
        </p>
    </section>

    {{-- サマリー --}}
    <section class="rounded border bg-white p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Progress</div>
                <div class="text-lg font-bold">解除状況</div>
            </div>

            <div class="text-right">
                <div class="text-2xl font-bold">
                    {{ number_format($unlockedCount) }} / {{ number_format($totalCount) }}
                </div>
                <div class="text-xs text-gray-500">
                    解除済み / 全実績
                </div>
            </div>
        </div>

        @php
            $rate = $totalCount > 0 ? (int) floor(($unlockedCount / $totalCount) * 100) : 0;
        @endphp

        <div class="mt-3 h-2 w-full rounded bg-gray-100 overflow-hidden">
            <div class="h-full bg-gray-800" style="width: {{ $rate }}%"></div>
        </div>

        <div class="mt-2 text-xs text-gray-500">
            ※実績の追加・内容変更は今後も行う可能性があります
        </div>
    </section>

    {{-- 実績一覧 --}}
    <section class="rounded border bg-white">
        <div class="border-b px-4 py-3 flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Achievements</div>
                <div class="font-bold">一覧</div>
            </div>

            <div class="text-xs text-gray-500">
                ※解除済みは上に表示
            </div>
        </div>

        <div class="p-4">
            @if ($achievements->isEmpty())
                <div class="text-sm text-gray-600">
                    実績がまだ登録されていません。<br>
                    achievements テーブルにマスタを入れると表示されます。
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($achievements as $achievement)
                        @php
                            // Controller側で unlocked_at を付けて渡すのが理想だが、
                            // ここでは achievement->unlocked_at がある前提で書く（なければ未解除扱い）
                            $unlockedAt = $achievement->unlocked_at ?? null;
                            $isUnlocked = !empty($unlockedAt);

                            $conditionType = $achievement->condition_type ?? null;
                            $conditionValue = $achievement->condition_value ?? null;

                            $conditionLabel = $conditionTypeLabel[$conditionType] ?? ($conditionType ?? '条件');
                        @endphp

                        <div class="rounded border p-4 {{ $isUnlocked ? 'bg-white' : 'bg-gray-50' }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    {{-- タイトル --}}
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-lg truncate">
                                            {{ $achievement->name ?? '（実績名未設定）' }}
                                        </div>

                                        @if ($isUnlocked)
                                            <span class="text-xs px-2 py-0.5 rounded border bg-green-50 border-green-200">
                                                解除済み
                                            </span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 rounded border bg-gray-100 border-gray-200">
                                                未解除
                                            </span>
                                        @endif
                                    </div>

                                    {{-- 説明 --}}
                                    @if (!empty($achievement->description))
                                        <div class="text-sm text-gray-700 mt-1">
                                            {{ $achievement->description }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 mt-1">
                                            （説明なし）
                                        </div>
                                    @endif

                                    {{-- 条件 --}}
                                    <div class="mt-2 text-xs text-gray-500">
                                        条件：{{ $conditionLabel }}
                                        @if (!is_null($conditionValue))
                                            {{ $conditionValue }}
                                        @endif
                                    </div>

                                    {{-- code --}}
                                    @if (!empty($achievement->code))
                                        <div class="mt-1 text-xs text-gray-400">
                                            code: {{ $achievement->code }}
                                        </div>
                                    @endif
                                </div>

                                {{-- 解除日 --}}
                                <div class="text-right whitespace-nowrap">
                                    <div class="text-xs text-gray-500">Unlocked</div>

                                    @if ($isUnlocked)
                                        <div class="font-semibold">
                                            {{ \Carbon\Carbon::parse($unlockedAt)->format('Y-m-d') }}
                                        </div>
                                    @else
                                        <div class="text-gray-400">
                                            -
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 導線 --}}
    <section class="rounded border bg-white p-4">
        <div class="space-y-2">
            <div class="font-bold">実績を増やすには</div>

            <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                <li>
                    <a href="{{ route('tasks.index') }}" class="underline">
                        タスクを完了して討伐待ちに送る
                    </a>
                </li>
                <li>
                    <a href="{{ route('taskkill.index') }}" class="underline">
                        TaskKillで討伐して、戦歴を積む
                    </a>
                </li>
                <li>
                    <a href="{{ route('status.index') }}" class="underline">
                        ステータスを伸ばす
                    </a>
                </li>
            </ul>
        </div>
    </section>

</div>
@endsection
