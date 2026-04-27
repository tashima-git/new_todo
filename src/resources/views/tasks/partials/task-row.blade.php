@php
    $statusValue = $task->status->value;
    $bossType = $task->boss_type->value;
    $hasChildren = $task->childTasks->count() > 0;
    $canHaveChildren = in_array($bossType, ['mid', 'boss'], true);

    // ★ 追加：表示モード
    $view = request('view', 'tree');
@endphp

<tr
    class="task-row level-{{ $level }} {{ $bossType }}"
    data-id="{{ $task->id }}"
    @if($parentId && $view === 'tree')
        data-parent="{{ $parentId }}"
    @endif
>
    <td>
        <input type="checkbox" name="task_ids[]" value="{{ $task->id }}" class="task-check">
    </td>

    <td style="padding-left: {{ $view === 'tree' ? $level * 20 : 0 }}px;">

        {{-- ===============================
            アコーディオンボタン（ツリーのみ）
        =============================== --}}
        @if($view === 'tree' && $status === 'pending' && $hasChildren && $canHaveChildren)
            <button type="button"
                class="toggle-children"
                data-target="{{ $task->id }}"
                data-open="false">
                ▶
            </button>
        @endif

        {{ $task->title }}
    </td>

    <td>
        {{ $task->category->value === 'work' ? '仕事・学校' : 'プライベート' }}
    </td>

    <td>
        {{ $task->due_date?->format('Y/m/d') ?? '-' }}
    </td>

    <td style="text-align:center;">
        {{ $task->importance ?? '-' }}
    </td>

    <td style="text-align:center;">
        {{ $task->is_urgent ? '急ぎ' : '-' }}
    </td>

    <td style="text-align:center;">
        @if ($bossType === 'mob')
            雑魚
        @elseif ($bossType === 'mid')
            中ボス
        @else
            大ボス
        @endif
    </td>

    <td>
        <div class="task-actions">

            @if($statusValue === 'pending')

            <a href="{{ route('tasks.edit', $task) }}" class="btn-edit">編集</a>

                <button type="button"
                    class="btn-complete"
                    data-url="{{ route('tasks.complete', $task) }}">
                    完了
                </button>

                <button type="button"
                    class="btn-delete"
                    data-url="{{ route('tasks.destroy', $task) }}">
                    削除
                </button>

            @else

                <button type="button"
                    class="btn-uncomplete"
                    data-url="{{ route('tasks.uncomplete', $task) }}">
                    未完了
                </button>

            @endif

            @if($canHaveChildren)
                <a href="{{ route('tasks.create', ['parent_task_id' => $task->id]) }}"
                   class="btn-child">
                    ＋配下
                </a>
            @endif

        </div>
    </td>
</tr>

{{-- ===============================
    再帰表示（ツリーのみ）
=============================== --}}
@if($view === 'tree' && $hasChildren)
    @foreach($task->childTasks as $child)
        @include('tasks.partials.task-row', [
            'task' => $child,
            'level' => $level + 1,
            'parentId' => $task->id
        ])
    @endforeach
@endif