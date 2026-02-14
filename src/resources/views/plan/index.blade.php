@extends('layouts.app')

@section('title', 'プラン')

@section('content')
<div class="space-y-6">

    {{-- 見出し --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">プラン</h1>
            <p class="text-sm text-gray-600 mt-1">
                現在のプラン状況を確認できます。変更もここから行えます。
            </p>
        </div>

        <div class="text-right">
            <div class="text-xs text-gray-500">現在のプラン</div>
            <div class="text-lg font-bold">
                {{ $plan->plan_name ?? 'free' }}
            </div>
        </div>
    </div>

    {{-- 現在のプラン詳細 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="font-bold text-lg">現在のプラン内容</h2>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="rounded border p-4">
                <div class="text-sm text-gray-500">プラン名</div>
                <div class="text-xl font-bold mt-1">
                    {{ $plan->plan_name ?? 'free' }}
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="text-sm text-gray-500">ステータス</div>
                <div class="text-xl font-bold mt-1">
                    {{ ($plan->is_active ?? true) ? '有効' : '無効' }}
                </div>
            </div>
        </div>

        <div class="mt-5 text-sm text-gray-700 leading-relaxed">
            <ul class="list-disc pl-5 space-y-1">
                <li>無料プランでも基本機能（タスク管理・討伐・ステータス反映）は利用できます。</li>
                <li>将来的に「保存できる討伐ログ数」などの制限を追加する想定です。</li>
            </ul>
        </div>
    </section>

    {{-- プラン変更 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="font-bold text-lg">プラン変更</h2>
        <p class="text-sm text-gray-600 mt-1">
            変更はすぐ反映されます。※ 現時点では課金処理は未実装です。
        </p>

        <form method="POST" action="{{ route('plan.update') }}" class="mt-5 space-y-4">
            @csrf

            {{-- 選択 --}}
            <div class="grid gap-4 md:grid-cols-2">

                {{-- FREE --}}
                <label class="block rounded border p-4 cursor-pointer hover:bg-gray-50">
                    <div class="flex items-start gap-3">
                        <input
                            type="radio"
                            name="plan_name"
                            value="free"
                            class="mt-1"
                            {{ ($plan->plan_name ?? 'free') === 'free' ? 'checked' : '' }}
                        >

                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs text-gray-500">FREE</div>
                                    <div class="text-lg font-bold">無料プラン</div>
                                </div>
                                <div class="text-sm font-bold">¥0</div>
                            </div>

                            <div class="mt-3 text-sm text-gray-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>タスク作成・完了・討伐（TaskKill）</li>
                                    <li>ステータス反映</li>
                                    <li>統計閲覧（軽量版）</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </label>

                {{-- PRO --}}
                <label class="block rounded border p-4 cursor-pointer hover:bg-gray-50">
                    <div class="flex items-start gap-3">
                        <input
                            type="radio"
                            name="plan_name"
                            value="pro"
                            class="mt-1"
                            {{ ($plan->plan_name ?? 'free') === 'pro' ? 'checked' : '' }}
                        >

                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs text-gray-500">PRO</div>
                                    <div class="text-lg font-bold">有料プラン</div>
                                </div>
                                <div class="text-sm font-bold">¥???</div>
                            </div>

                            <div class="mt-3 text-sm text-gray-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>討伐ログの保存数上限アップ（予定）</li>
                                    <li>統計の詳細表示（予定）</li>
                                    <li>演出カスタム（予定）</li>
                                </ul>
                            </div>

                            <div class="mt-3 text-xs text-gray-500">
                                ※ 現時点では見た目だけです（課金機能は未実装）
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            {{-- 送信 --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-white text-sm hover:opacity-90">
                    プランを変更する
                </button>
            </div>
        </form>
    </section>

    {{-- 注意 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="font-bold text-lg">注意</h2>
        <div class="mt-3 text-sm text-gray-700 leading-relaxed space-y-2">
            <p>
                TaskKillは「軽さ」と「継続性」を優先して作っています。
                そのため、保存データは必要最低限に抑える方針です。
            </p>
            <p>
                有料プランは「保存できる量」や「見返しやすさ」を拡張する方向で考えています。
            </p>
        </div>
    </section>

</div>
@endsection
