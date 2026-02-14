@extends('layouts.app')

@section('title', 'ステータス')

@section('content')
@php
    // Controllerから$userが来ていない場合でも動くように保険
    $user = $user ?? auth()->user();

    $stats = [
        'patience' => [
            'label_ja' => '忍耐',
            'label_en' => 'Patience',
            'value' => $user->total_patience ?? 0,
            'desc' => '継続力・我慢強さ',
        ],
        'speed' => [
            'label_ja' => '素早さ',
            'label_en' => 'Speed',
            'value' => $user->total_speed ?? 0,
            'desc' => '行動の速さ・着手力',
        ],
        'focus' => [
            'label_ja' => '集中',
            'label_en' => 'Focus',
            'value' => $user->total_focus ?? 0,
            'desc' => '一点突破・没頭力',
        ],
        'accuracy' => [
            'label_ja' => '正確',
            'label_en' => 'Accuracy',
            'value' => $user->total_accuracy ?? 0,
            'desc' => 'ミスの少なさ・丁寧さ',
        ],
        'life' => [
            'label_ja' => '生命',
            'label_en' => 'Life',
            'value' => $user->total_life ?? 0,
            'desc' => '体力・粘り強さ',
        ],
        'strategy' => [
            'label_ja' => '戦略',
            'label_en' => 'Strategy',
            'value' => $user->total_strategy ?? 0,
            'desc' => '計画性・工夫',
        ],
    ];

    $total = collect($stats)->sum('value');
    $max = max(1, collect($stats)->max('value')); // 0割防止
@endphp

<div class="space-y-6">

    {{-- タイトル --}}
    <section class="space-y-1">
        <h1 class="text-2xl font-bold">ステータス</h1>
        <p class="text-sm text-gray-600">
            討伐で得た成長が、ここに蓄積されます。
        </p>
    </section>

    {{-- サマリー --}}
    <section class="rounded border bg-white p-4 space-y-2">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">合計ステータス</div>
                <div class="text-2xl font-bold">{{ number_format($total) }}</div>
            </div>

            <div class="text-right">
                <div class="text-sm text-gray-500">現在の冒険者</div>
                <div class="font-semibold">{{ $user->name ?? 'Player' }}</div>
            </div>
        </div>

        <div class="text-xs text-gray-500">
            ※この数値は「討伐結果」で加算されます（タスク作成だけでは増えません）
        </div>
    </section>

    {{-- ステ一覧 --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($stats as $key => $s)
            @php
                $value = $s['value'];
                $rate = (int) floor(($value / $max) * 100);
            @endphp

            <div class="rounded border bg-white p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs text-gray-500 tracking-wide">
                            {{ $s['label_en'] }}
                        </div>
                        <div class="text-lg font-bold">
                            {{ $s['label_ja'] }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $s['desc'] }}
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-2xl font-bold">
                            {{ number_format($value) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            累計
                        </div>
                    </div>
                </div>

                {{-- ゲージ（CSSはTailwindの範囲だけ。色などを別CSSにしたいなら後で外せる） --}}
                <div class="h-2 w-full rounded bg-gray-100 overflow-hidden">
                    <div class="h-full bg-gray-800" style="width: {{ $rate }}%"></div>
                </div>

                <div class="flex justify-between text-xs text-gray-500">
                    <span>0</span>
                    <span>{{ number_format($max) }}</span>
                </div>
            </div>
        @endforeach
    </section>

    {{-- 次の導線 --}}
    <section class="rounded border bg-white p-4">
        <div class="space-y-2">
            <div class="font-bold">次にできること</div>

            <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                <li>
                    <a href="{{ route('tasks.index') }}" class="underline">
                        タスクを作って、完了して、討伐待ちに送る
                    </a>
                </li>
                <li>
                    <a href="{{ route('taskkill.index') }}" class="underline">
                        TaskKillでまとめて討伐して、ステータスを伸ばす
                    </a>
                </li>
                <li>
                    <a href="{{ route('stats.index') }}" class="underline">
                        討伐記録（戦歴）を振り返る
                    </a>
                </li>
            </ul>
        </div>
    </section>

</div>
@endsection
