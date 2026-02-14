@extends('layouts.app')

@section('title', 'タスク作成')

@section('content')
    <div class="tk-page">

        {{-- ページタイトル --}}
        <div class="tk-page__header">
            <div>
                <div class="tk-page__en">Create</div>
                <h1 class="tk-page__title">タスク作成</h1>
            </div>

            <div class="tk-page__actions">
                <a href="{{ route('tasks.index') }}" class="tk-btn tk-btn--ghost">
                    ← 一覧へ戻る
                </a>
            </div>
        </div>

        <div class="tk-card">
            <form method="POST" action="{{ route('tasks.store') }}" class="tk-form">
                @csrf

                {{-- タスク名 --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="title">タスク名 <span class="tk-required">*</span></label>
                    <input type="text" id="title" name="title" class="tk-input"
                        value="{{ old('title') }}" required maxlength="255"
                        placeholder="例：Laravelのタスク一覧を作る">
                    <div class="tk-help">
                        ※ なるべく「動詞」で書くと達成しやすい
                    </div>
                </div>

                {{-- カテゴリ --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="category">カテゴリ <span class="tk-required">*</span></label>
                    <select id="category" name="category" class="tk-select" required>
                        <option value="work" {{ old('category', 'work') === 'work' ? 'selected' : '' }}>
                            仕事・学校
                        </option>
                        <option value="private" {{ old('category') === 'private' ? 'selected' : '' }}>
                            プライベート
                        </option>
                    </select>
                </div>

                {{-- 期限 --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="due_date">期限（任意）</label>
                    <input type="date" id="due_date" name="due_date" class="tk-input"
                        value="{{ old('due_date') }}">
                    <div class="tk-help">
                        ※ 完了しても TaskKill するまでは残ります（誤操作の復旧用）
                    </div>
                </div>

                {{-- 重要度 --}}
                <div class="tk-form__group">
                    <label class="tk-label" for="importance">重要度 <span class="tk-required">*</span></label>
                    <select id="importance" name="importance" class="tk-select" required>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ (int)old('importance', 3) === $i ? 'selected' : '' }}>
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
                        <input type="checkbox" name="is_urgent" value="1"
                            {{ old('is_urgent') ? 'checked' : '' }}>
                        <span>急ぎ（優先して片付けたい）</span>
                    </label>
                </div>

                <hr class="tk-hr">

                {{-- ステータス割り振り --}}
                <div class="tk-section">
                    <div class="tk-section__title">
                        <div class="tk-section__en">Stats</div>
                        <div class="tk-section__ja">ステータス割り振り</div>
                    </div>

                    <div class="tk-help">
                        ※ 0でもOK。迷ったら「おすすめ」を押せばOK。
                    </div>

                    {{-- おすすめボタン --}}
                    <div class="tk-row tk-row--gap">
                        <button type="button" class="tk-btn tk-btn--sub" id="btnRecommend">
                            おすすめ割り振り
                        </button>

                        <button type="button" class="tk-btn tk-btn--ghost" id="btnClearStats">
                            全部0にする
                        </button>
                    </div>

                    <div class="tk-grid tk-grid--2">
                        {{-- patience --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_patience">忍耐（patience）</label>
                            <input type="number" id="stat_patience" name="stat_patience" class="tk-input"
                                value="{{ old('stat_patience', 0) }}" min="0" max="999">
                            <div class="tk-help">筋トレ、有酸素運動など</div>
                        </div>

                        {{-- speed --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_speed">迅速（speed）</label>
                            <input type="number" id="stat_speed" name="stat_speed" class="tk-input"
                                value="{{ old('stat_speed', 0) }}" min="0" max="999">
                            <div class="tk-help">返信、報告、素早い処理</div>
                        </div>

                        {{-- focus --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_focus">集中（focus）</label>
                            <input type="number" id="stat_focus" name="stat_focus" class="tk-input"
                                value="{{ old('stat_focus', 0) }}" min="0" max="999">
                            <div class="tk-help">勉強、会議、集中作業</div>
                        </div>

                        {{-- accuracy --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_accuracy">正確（accuracy）</label>
                            <input type="number" id="stat_accuracy" name="stat_accuracy" class="tk-input"
                                value="{{ old('stat_accuracy', 0) }}" min="0" max="999">
                            <div class="tk-help">資料作成、設計、連絡</div>
                        </div>

                        {{-- life --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_life">生活力（life）</label>
                            <input type="number" id="stat_life" name="stat_life" class="tk-input"
                                value="{{ old('stat_life', 0) }}" min="0" max="999">
                            <div class="tk-help">買い物、家事、生活タスク</div>
                        </div>

                        {{-- strategy --}}
                        <div class="tk-form__group">
                            <label class="tk-label" for="stat_strategy">戦略（strategy）</label>
                            <input type="number" id="stat_strategy" name="stat_strategy" class="tk-input"
                                value="{{ old('stat_strategy', 0) }}" min="0" max="999">
                            <div class="tk-help">準備、整備、段取り</div>
                        </div>
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

@section('js')
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
