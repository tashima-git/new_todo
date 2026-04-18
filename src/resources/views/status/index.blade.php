@extends('layouts.app')

@section('title', 'ステータス')

@section('css')
<link rel="stylesheet" href="/css/status.css">
@endsection


@section('content')

@php

$user = $user ?? auth()->user();

$stats = [

    'patience' => [
        'label_ja' => '忍耐',
        'value' => $user->total_patience ?? 0,
    ],

    'speed' => [
        'label_ja' => '素早さ',
        'value' => $user->total_speed ?? 0,
    ],

    'focus' => [
        'label_ja' => '集中力',
        'value' => $user->total_focus ?? 0,
    ],

    'accuracy' => [
        'label_ja' => '正確さ',
        'value' => $user->total_accuracy ?? 0,
    ],

    'life' => [
        'label_ja' => '生活力',
        'value' => $user->total_life ?? 0,
    ],

    'strategy' => [
        'label_ja' => '戦略',
        'value' => $user->total_strategy ?? 0,
    ],

];

$total = collect($stats)->sum('value');

$max = max(1, collect($stats)->max('value'));



/* ===============================
   レーダー計算
=============================== */

$centerX = 100;
$centerY = 100;
$radius = 70;

$values = collect($stats)
->map(fn($s)=> $s['value'] / $max)
->values();


/* 外枠 */
$basePoints = [];

for($i=0;$i<6;$i++){

    $angle = deg2rad(60*$i - 90);

    $x = $centerX + cos($angle)*$radius;
    $y = $centerY + sin($angle)*$radius;

    $basePoints[] = "$x,$y";
}


/* ステータス形状 */
$valuePoints = [];

for($i=0;$i<6;$i++){

    $angle = deg2rad(60*$i - 90);

    $r = $radius * $values[$i];

    $x = $centerX + cos($angle)*$r;
    $y = $centerY + sin($angle)*$r;

    $valuePoints[] = "$x,$y";
}


/* ラベル */
$labels = [];

$i = 0;

foreach($stats as $s){

    $angle = deg2rad(60*$i - 90);

    $labelRadius = $radius + 22;

    $x = $centerX + cos($angle)*$labelRadius;
    $y = $centerY + sin($angle)*$labelRadius;

    $labels[] = [
        'x'=>$x,
        'y'=>$y,
        'text'=>$s['label_ja']
    ];

    $i++;
}

@endphp



<div class="status">


    {{-- header --}}
    <section class="status-header">

        <h1>ステータス</h1>

    </section>



    {{-- summary --}}
    <section class="status-summary">

        <div class="status-summary-row">

            <div>

                <div class="status-label">
                    合計ステータス
                </div>

                <div class="status-total">
                    {{ number_format($total) }}
                </div>

            </div>


            <div>

                <div class="status-label">
                    冒険者
                </div>

                <div class="status-name">
                    {{ $user->name ?? 'Player' }}
                </div>

            </div>

        </div>

    </section>



    {{-- radar --}}
    <section class="status-radar">

        <svg viewBox="0 0 200 200" class="radar">

            <!-- 外枠 -->
            <polygon
                points="{{ implode(' ', $basePoints) }}"
                class="radar-base"
            />

            <!-- 放射線 -->
            @for($i=0;$i<6;$i++)

            @php

                $angle = deg2rad(60*$i - 90);

                $x = $centerX + cos($angle)*$radius;
                $y = $centerY + sin($angle)*$radius;

            @endphp

            <line
                x1="100"
                y1="100"
                x2="{{ $x }}"
                y2="{{ $y }}"
                class="radar-line"
            />

            @endfor


            <!-- ステータス -->
            <polygon
                points="100,100 100,100 100,100 100,100 100,100 100,100"
                data-points="{{ implode(' ', $valuePoints) }}"
                class="radar-value"
                id="radarValue"
            />


            <!-- ラベル -->
            @foreach($labels as $label)

            <text
                x="{{ $label['x'] }}"
                y="{{ $label['y'] }}"
                class="radar-label"
            >

                {{ $label['text'] }}

            </text>

            @endforeach


        </svg>

    </section>



    {{-- cards --}}
    <section class="status-grid">

        @foreach ($stats as $s)

        <div class="status-card">


            <div class="status-card-header">

                <div class="status-ja">

                    {{ $s['label_ja'] }}

                </div>


                <div class="status-value">

                    {{ number_format($s['value']) }}

                </div>


            </div>



            <div class="status-bar">

                <div
                    class="status-bar-fill"
                    data-width="{{ (int)(($s['value']/$max)*100) }}"
                ></div>

            </div>


        </div>

        @endforeach

    </section>



</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const audio = new Audio("/sounds/status.mp3");
    audio.volume = 0.3;

    audio.play().catch(() => {
    });

    const el = document.getElementById("radarValue");
    if (!el) return;

    const finalPoints = el.dataset.points
        .split(" ")
        .map(p => p.split(",").map(Number));

    const duration = 1000;
    const start = performance.now();

    function animate(now) {

        const t = Math.min((now - start) / duration, 1);

        // ease-out
        const ease = 1 - Math.pow(1 - t, 3);

        const points = finalPoints.map(([x, y]) => {
            const cx = 100;
            const cy = 100;

            const nx = cx + (x - cx) * ease;
            const ny = cy + (y - cy) * ease;

            return `${nx},${ny}`;
        });

        el.setAttribute("points", points.join(" "));

        if (t < 1) requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);


    setTimeout(() => {
        document.querySelectorAll('.status-bar-fill').forEach(el => {
            const w = el.dataset.width;
            el.style.width = w + '%';
        });
    }, 100);

});
</script>

@endsection