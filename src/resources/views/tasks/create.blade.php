@extends('layouts.app')

@section('title', 'タスク作成')

@section('css')
<link rel="stylesheet" href="/css/task-create.css">
@endsection

@section('content')

@php
$parentTaskId = old('parent_task_id', request('parent_task_id'));
$hasParent = !empty($parentTaskId);
@endphp

<div class="tk-page">

{{-- ヘッダー --}}
<div class="tk-page__header">
    <div>
        <h1 class="tk-page__title">
            タスク作成
            @if ($hasParent)
                <span class="tk-text-sm tk-muted">（配下）</span>
            @endif
        </h1>
    </div>
</div>

{{-- 親タスク表示 --}}
@if ($hasParent)
    <div class="tk-card tk-card--mb">
        <div class="tk-row tk-row--start tk-row--gap">

            <div class="tk-strong">配下タスク</div>

            <div>
                @isset($parentTask)
                    <div class="tk-text-sm tk-mt-6">
                        親タスク: <b>{{ $parentTask->title }}</b>
                    </div>
                @endisset

                <div class="tk-text-xs tk-muted tk-mt-6">
                    ※ このタスクは「中ボス / 大ボス」の配下として登録されます
                </div>
            </div>

        </div>
    </div>
@endif

{{-- フォーム --}}
<div class="tk-card">
    <form method="POST" action="{{ route('tasks.store') }}" class="tk-form">
        @csrf

        @if ($hasParent)
            <input type="hidden" name="parent_task_id" value="{{ $parentTaskId }}">
        @endif

        {{-- エラー --}}
        @if ($errors->any())
            <div class="tk-card tk-card--mb">
                <div class="tk-strong">入力に問題があります</div>
                <ul class="tk-mt-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- タスク名 --}}
        <div class="tk-form__group">
            <label class="tk-label" for="title">
                タスク名 <span class="tk-required">*</span>
            </label>
            <input
                type="text"
                id="title"
                name="title"
                class="tk-input"
                value="{{ old('title') }}"
                required
                maxlength="255"
                placeholder="例：資料作成、買い物、洗濯など"
            >
            <div class="tk-help">
                ※ なるべく「動詞」で書くと、やるべきことが明確になります
            </div>
        </div>

        {{-- メモ --}}
        <div class="tk-form__group">
            <label class="tk-label" for="memo">メモ・内容</label>
            <textarea
                id="memo"
                name="memo"
                class="tk-input tk-textarea"
                maxlength="2000"
                rows="4"
                placeholder="必要な手順、補足、完了条件などを自由に書けます"
            >{{ old('memo') }}</textarea>
            <div class="tk-help">
                ※ 任意入力です。タスク一覧にも短く表示されます
            </div>
        </div>

        <div class="tk-form-grid tk-form-grid--meta">
            {{-- カテゴリ --}}
            <div class="tk-form__group">
                <label class="tk-label" for="category">
                    カテゴリ <span class="tk-required">*</span>
                </label>
                <select id="category" name="category" class="tk-select" required>
                    <option value="work" {{ old('category','work')==='work'?'selected':'' }}>
                        仕事・学校
                    </option>
                    <option value="private" {{ old('category')==='private'?'selected':'' }}>
                        プライベート
                    </option>
                </select>
            </div>

            {{-- 期限 --}}
            <div class="tk-form__group">
                <label class="tk-label" for="due_date">期限</label>
                <div class="tk-date-field">
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        class="tk-input tk-date-input"
                        min="{{ now()->toDateString() }}"
                        value="{{ old('due_date') }}"
                    >
                    <button type="button" class="tk-date-button" data-date-picker-target="due_date">
                        日付を選ぶ
                    </button>
                </div>
            </div>

            {{-- 重要度 --}}
            <div class="tk-form__group">
                <label class="tk-label" for="importance">
                    重要度 <span class="tk-required">*</span>
                </label>
                <select id="importance" name="importance" class="tk-select" required>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ (int)old('importance',3)===$i?'selected':'' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <div class="tk-help">
                    1=低 / 3=普通 / 5=最重要
                </div>
            </div>

            {{-- 緊急 --}}
            <div class="tk-form__group">
                <label class="tk-label">緊急</label>
                <label class="tk-check">
                    <input type="hidden" name="is_urgent" value="0">
                    <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent')?'checked':'' }}>
                    <span>急ぎ（優先して片付けたい）</span>
                </label>
            </div>
        </div>

        <hr class="tk-hr">

        {{-- ステータス --}}
        <div class="tk-section">

            <div class="tk-section__title">
                <div class="tk-section__ja">ステータス割り振り</div>
            </div>

            <div class="tk-help">
                MVPでは合計0〜6ポイントの範囲で割り振ります。全て0でも登録できます。
            </div>

            <div class="tk-row tk-row--gap">
                <button type="button" class="tk-btn tk-btn--sub" id="btnRecommend">
                    おすすめ割り振り
                </button>

                <button type="button" class="tk-btn tk-btn--ghost" id="btnClearStats">
                    全部0にする
                </button>
            </div>

            <div class="tk-grid tk-grid--2">

                @foreach ([
                    'patience'=>'忍耐',
                    'speed'=>'迅速',
                    'focus'=>'集中',
                    'accuracy'=>'正確',
                    'life'=>'生活力',
                    'strategy'=>'戦略'
                ] as $key => $label)

                    <div class="tk-form__group">
                        <label class="tk-label" for="stat_{{ $key }}">
                            {{ $label }}
                        </label>

                        @if ($key === 'strategy')
                            <div class="tk-stat-input-with-side">
                                <input
                                    type="number"
                                    id="stat_{{ $key }}"
                                    name="stat_{{ $key }}"
                                    class="tk-input"
                                    value="{{ old('stat_'.$key,0) }}"
                                    min="0"
                                    max="6"
                                >
                                <span class="tk-stat-auto-bonus">
                                    +{{ $autoStrategyOnCreate }}
                                </span>
                            </div>
                        @else
                            <input
                                type="number"
                                id="stat_{{ $key }}"
                                name="stat_{{ $key }}"
                                class="tk-input"
                                value="{{ old('stat_'.$key,0) }}"
                                min="0"
                                max="6"
                            >
                        @endif
                    </div>

                @endforeach

            </div>

        </div>

        <hr class="tk-hr">

        {{-- 送信 --}}
        <div class="tk-row tk-row--right tk-row--gap">
            <a href="{{ route('tasks.index') }}" class="tk-btn tk-btn--ghost">
                キャンセル
            </a>
            <button type="submit" class="tk-btn tk-btn--primary">
                作成する
            </button>
        </div>

    </form>
</div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const category = document.getElementById('category');

    const statPatience = document.getElementById('stat_patience');
    const statSpeed = document.getElementById('stat_speed');
    const statFocus = document.getElementById('stat_focus');
    const statAccuracy = document.getElementById('stat_accuracy');
    const statLife = document.getElementById('stat_life');
    const statStrategy = document.getElementById('stat_strategy');

    const btnRecommend = document.getElementById('btnRecommend');
    const btnClearStats = document.getElementById('btnClearStats');

    document.querySelectorAll('[data-date-picker-target]').forEach(function(button) {
        button.addEventListener('click', function() {
            const input = document.getElementById(button.dataset.datePickerTarget);
            if (!input) return;

            if (typeof input.showPicker === 'function') {
                input.showPicker();
                return;
            }

            input.focus();
        });
    });

    function setStats(p, s, f, a, l, st) {
        statPatience.value = p;
        statSpeed.value = s;
        statFocus.value = f;
        statAccuracy.value = a;
        statLife.value = l;
        statStrategy.value = st;
    }

    // MVP用：カテゴリ別おすすめ（後で調整OK）
    function recommendByCategory() {
        const cat = category.value;

        // work：集中・正確・戦略寄り
        if (cat === 'work') {
            setStats(0, 1, 2, 2, 0, 1);
            return;
        }

        // private：生活力・忍耐寄り
        if (cat === 'private') {
            setStats(1, 1, 0, 0, 2, 0);
            return;
        }

        setStats(0, 0, 0, 0, 0, 0);
    }

    btnRecommend.addEventListener('click', recommendByCategory);

    btnClearStats.addEventListener('click', function() {
        setStats(0, 0, 0, 0, 0, 0);
    });

})();
</script>
@endsection
