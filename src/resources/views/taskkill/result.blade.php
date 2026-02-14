@extends('layouts.app')

@section('title', '討伐結果')

@section('content')
    <div class="space-y-6">

        {{-- タイトル --}}
        <div>
            <h1 class="text-2xl font-bold">討伐結果</h1>
            <p class="text-sm text-gray-600 mt-1">
                今日の討伐が完了しました。ステータスが上昇しています。
            </p>
        </div>

        {{-- 合計ステ上昇 --}}
        <div class="rounded border bg-white p-4">
            <div class="font-bold mb-3">今回の獲得ステータス</div>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>忍耐</span>
                    <span class="font-bold">+{{ $totalGained['patience'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>迅速</span>
                    <span class="font-bold">+{{ $totalGained['speed'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>集中</span>
                    <span class="font-bold">+{{ $totalGained['focus'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>正確</span>
                    <span class="font-bold">+{{ $totalGained['accuracy'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>生活力</span>
                    <span class="font-bold">+{{ $totalGained['life'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span>戦略</span>
                    <span class="font-bold">+{{ $totalGained['strategy'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- 討伐ログ一覧 --}}
        <div class="rounded border bg-white p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="font-bold">討伐した敵</div>
                <div class="text-sm text-gray-600">
                    {{ count($logs) }}体
                </div>
            </div>

            @if (count($logs) === 0)
                <div class="text-sm text-gray-600">
                    討伐ログがありません。
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($logs as $log)
                        <div class="rounded border p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-bold">
                                        #{{ $log->id }}
                                        <span class="ml-2 text-sm font-normal text-gray-600">
                                            （{{ $log->boss_type }}）
                                        </span>
                                    </div>

                                    <div class="text-sm text-gray-700 mt-1">
                                        タスクID：{{ $log->task_id }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        討伐日時：
                                        {{ $log->completed_at ? $log->completed_at->format('Y-m-d H:i') : '-' }}
                                    </div>
                                </div>

                                {{-- ボス表示 --}}
                                <div class="text-sm">
                                    @if ($log->boss_type === 'boss')
                                        <span class="rounded border px-2 py-1">大ボス</span>
                                    @elseif ($log->boss_type === 'mid')
                                        <span class="rounded border px-2 py-1">中ボス</span>
                                    @else
                                        <span class="rounded border px-2 py-1">雑魚</span>
                                    @endif
                                </div>
                            </div>

                            {{-- 獲得ステータス --}}
                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>忍耐</span>
                                    <span class="font-bold">+{{ $log->gained_patience }}</span>
                                </div>
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>迅速</span>
                                    <span class="font-bold">+{{ $log->gained_speed }}</span>
                                </div>
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>集中</span>
                                    <span class="font-bold">+{{ $log->gained_focus }}</span>
                                </div>
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>正確</span>
                                    <span class="font-bold">+{{ $log->gained_accuracy }}</span>
                                </div>
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>生活力</span>
                                    <span class="font-bold">+{{ $log->gained_life }}</span>
                                </div>
                                <div class="flex items-center justify-between border rounded px-2 py-1">
                                    <span>戦略</span>
                                    <span class="font-bold">+{{ $log->gained_strategy }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 戻る --}}
        <div class="flex gap-3">
            <a href="{{ route('taskkill.index') }}" class="rounded border px-4 py-2 text-sm hover:bg-gray-100">
                討伐待ちへ戻る
            </a>

            <a href="{{ route('tasks.index') }}" class="rounded border px-4 py-2 text-sm hover:bg-gray-100">
                タスク一覧へ
            </a>

            <a href="{{ route('status.index') }}" class="rounded border px-4 py-2 text-sm hover:bg-gray-100">
                ステータスを見る
            </a>
        </div>
    </div>
@endsection
