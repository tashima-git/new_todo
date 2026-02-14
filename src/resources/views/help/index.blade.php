@extends('layouts.app')

@section('title', 'ヘルプ')

@section('content')
<div class="space-y-8">

    {{-- 見出し --}}
    <section>
        <h1 class="text-2xl font-bold">ヘルプ</h1>
        <p class="text-sm text-gray-600 mt-2">
            TaskKillの使い方・よくある質問をまとめています。
        </p>
    </section>

    {{-- まず何をすればいい？ --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="text-lg font-bold">まず何をすればいい？</h2>

        <div class="mt-4 space-y-3 text-sm text-gray-700 leading-relaxed">
            <div class="rounded border p-4">
                <div class="font-bold">1. タスクを作る</div>
                <div class="mt-1 text-gray-600">
                    タスクは「倒す敵」です。期限・難度・ステ割り振りを設定できます。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">2. 完了にする（討伐待ちにする）</div>
                <div class="mt-1 text-gray-600">
                    タスクを終えたら「完了」にします。ここではまだ削除されません。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">3. TaskKillで討伐する</div>
                <div class="mt-1 text-gray-600">
                    討伐するとステータスが上がり、討伐ログが残ります。
                    討伐が完了したタスクはこの時点で整理されます。
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('tasks.index') }}" class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                タスク一覧へ
            </a>
            @auth
                <a href="{{ route('taskkill.index') }}" class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                    TaskKillへ
                </a>
            @endauth
        </div>
    </section>

    {{-- 用語 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="text-lg font-bold">用語</h2>

        <div class="mt-4 grid gap-4 md:grid-cols-2 text-sm">
            <div class="rounded border p-4">
                <div class="font-bold">未完了（pending）</div>
                <div class="mt-1 text-gray-600">
                    まだ終わっていないタスクです。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">討伐待ち（stocked）</div>
                <div class="mt-1 text-gray-600">
                    タスク自体は完了した状態です。TaskKillで討伐すると処理が確定します。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">討伐ログ（TaskKillLog）</div>
                <div class="mt-1 text-gray-600">
                    討伐した記録です。統計やステータスの振り返りに使われます。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">中ボス / 大ボス</div>
                <div class="mt-1 text-gray-600">
                    難度や期限などから分類される強敵枠です。討伐後は統計などで振り返れます。
                </div>
            </div>
        </div>
    </section>

    {{-- よくある質問 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="text-lg font-bold">よくある質問（FAQ）</h2>

        <div class="mt-4 space-y-4 text-sm text-gray-700 leading-relaxed">

            <div class="rounded border p-4">
                <div class="font-bold">Q. 完了にしたら期限（due_date）は消えますか？</div>
                <div class="mt-2 text-gray-600">
                    いいえ。完了した瞬間は残ります。<br>
                    TaskKillで討伐が終わった時点で、ログに必要な情報だけ保存して整理されます。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">Q. 間違って完了にした場合は戻せますか？</div>
                <div class="mt-2 text-gray-600">
                    戻せます。討伐前なら「未完了に戻す」が可能です。<br>
                    再度完了にした場合、完了日時は「その時点の時刻」で更新されます。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">Q. タスクを削除できるのはどれ？</div>
                <div class="mt-2 text-gray-600">
                    基本的に「未完了（pending）」のみ削除できます。<br>
                    完了（討伐待ち）にしたものは、事故防止のため削除できない設計です。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">Q. TaskKill画面でチェックボックスは必要？</div>
                <div class="mt-2 text-gray-600">
                    いいえ。TaskKill画面は「討伐待ちのタスクを自動で集めて討伐する」画面です。<br>
                    チェックボックスはタスク一覧での一括操作用です。
                </div>
            </div>

            <div class="rounded border p-4">
                <div class="font-bold">Q. 過去のタスクはどこで見返せますか？</div>
                <div class="mt-2 text-gray-600">
                    討伐後の記録は「統計」や「ステータス」で振り返れます。<br>
                    過去タスクの詳細（カテゴリやステ割り振りなど）は表示せず、
                    軽量にする方針です。
                </div>
            </div>

        </div>
    </section>

    {{-- 連絡 --}}
    <section class="rounded-lg border bg-white p-5">
        <h2 class="text-lg font-bold">困ったら</h2>
        <div class="mt-3 text-sm text-gray-700 leading-relaxed space-y-2">
            <p>
                もし挙動が変だったり、意図しない結果になった場合は、
                まず「タスク一覧」と「TaskKill結果」を確認してください。
            </p>
            <p class="text-gray-600">
                （将来的に、ここに問い合わせフォームやフィードバック機能を追加できます）
            </p>
        </div>
    </section>

</div>
@endsection
