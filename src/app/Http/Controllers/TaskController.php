<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Enums\TaskStatus;
use App\Enums\BossType;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\BulkTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * 一覧（タブ切替対応）
     */
public function index(Request $request)
{
    $user = Auth::user();
    $userSetting = $user->setting;
    $status   = $request->input('status', 'pending');
    $category = $request->input('category');
    $bossType = $request->input('boss_type');
    $urgentOnly = $request->boolean('urgent_only');
    $sortDue  = $request->boolean('sort_due', false);
    $view     = $request->input('view', $userSetting?->default_task_view ?? 'tree'); // ★ 追加
    $perPage  = $userSetting?->tasks_per_page ?? 10;

    $query = $user->tasks()
        ->where(
            'status',
            $status === 'stocked'
                ? TaskStatus::Stocked->value
                : TaskStatus::Pending->value
        );

    /*
    |--------------------------------------------------------------------------
    | 表示モード制御（最重要）
    |--------------------------------------------------------------------------
    */

    if ($view === 'tree' && $status === 'pending') {

        // 未完了ツリーでは、未完了の親を持つ子だけを配下表示する。
        // 親が完了/討伐済みの場合、未完了の子は孤立させずルート扱いで表示する。
        $query->where(function ($q) use ($urgentOnly) {
            $q->whereNull('parent_task_id')
                ->orWhereDoesntHave('parentTask', function ($parentQuery) use ($urgentOnly) {
                    $parentQuery->where('status', TaskStatus::Pending->value);

                    if ($urgentOnly) {
                        $parentQuery->where('is_urgent', true);
                    }
                });
        })->with(['childTasks' => function ($q) use ($urgentOnly) {
            $q->where('status', TaskStatus::Pending->value);

            if ($urgentOnly) {
                $q->where('is_urgent', true);
            }

            $q->with(['childTasks' => function ($q) use ($urgentOnly) {
                $q->where('status', TaskStatus::Pending->value);

                if ($urgentOnly) {
                    $q->where('is_urgent', true);
                }

                $q->with(['childTasks' => function ($q) use ($urgentOnly) {
                    $q->where('status', TaskStatus::Pending->value);

                    if ($urgentOnly) {
                        $q->where('is_urgent', true);
                    }
                }]);
            }]);
        }]);

    } else {

        // フラット表示 or 完了タブ
        // → 親子関係を無視して全取得
        // （with不要＝軽量化）
    }

    /*
    |--------------------------------------------------------------------------
    | フィルタ
    |--------------------------------------------------------------------------
    */

    if ($category) {
        $query->where('category', $category);
    }

    if ($bossType) {
        $query->where('boss_type', $bossType);
    }

    if ($urgentOnly) {
        $query->where('is_urgent', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ソート
    |--------------------------------------------------------------------------
    */

    if ($view === 'flat') {

        // ★ フラット表示は強制的に期限優先
        $query->orderByRaw('due_date IS NULL')
              ->orderBy('due_date')
              ->orderByDesc('is_urgent');

    } else {

        if ($sortDue) {

            $query->orderByRaw('due_date IS NULL')
                  ->orderBy('due_date')
                  ->orderByDesc('is_urgent');

        } else {

            $query->orderByRaw("
                CASE boss_type
                    WHEN 'mob' THEN 1
                    WHEN 'mid' THEN 2
                    WHEN 'boss' THEN 3
                    ELSE 4
                END
            ")
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderByDesc('is_urgent');
        }
    }

    $tasks = $query->paginate($perPage)->withQueryString();

    return view('tasks.index', compact(
        'tasks',
        'status',
        'sortDue',
        'urgentOnly',
        'view' // ★ 追加
    ));
}

    /**
     * 作成画面
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $parentTaskId = $request->query('parent_task_id');
        $parentTask = null;
        $autoStrategyOnCreate = $user->setting?->auto_strategy_on_create ?? 1;

        if ($parentTaskId) {
            $parentTask = $user->tasks()->find($parentTaskId);
        }

        return view('tasks.create', compact('parentTaskId', 'parentTask', 'autoStrategyOnCreate'));
    }

    /**
     * 保存
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['is_urgent'] = $request->boolean('is_urgent');
        $data['boss_type'] = $this->decideBossType($data['due_date'] ?? null);

        if (!empty($data['parent_task_id'])) {
            $parent = Auth::user()->tasks()->find($data['parent_task_id']);
            if (!$parent) {
                $data['parent_task_id'] = null;
            }

            // 🔥 mobには子を付けられない
            if ($parent && $parent->boss_type === BossType::Mob) {
                $data['parent_task_id'] = null;
            }
        }

        Auth::user()->tasks()->create($data);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'タスクを作成しました。');
    }

    /**
     * 編集
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->status !== TaskStatus::Pending) {
            return redirect()
                ->route('tasks.index', ['status' => $task->status->value])
                ->with('error', '編集できるのは未完了タスクのみです。');
        }

        return view('tasks.edit', compact('task'));
    }

    /**
     * 更新
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        if ($task->status !== TaskStatus::Pending) {
            return redirect()
                ->route('tasks.index', ['status' => $task->status->value])
                ->with('error', '編集できるのは未完了タスクのみです。');
        }

        $data = $request->validated();
        $data['is_urgent'] = $request->boolean('is_urgent');

        if (array_key_exists('due_date', $data)) {
            $data['boss_type'] = $this->decideBossType($data['due_date']);
        }

        $task->update($data);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'タスクを更新しました。');
    }

    /**
     * 完了
     */
    public function complete(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        if ($task->status !== TaskStatus::Pending) {
            return back()->with('error', '完了にできるのは未完了タスクのみです。');
        }

        $completedAt = now();
        $taskIds = $this->descendantIds($task);
        $taskIds[] = $task->id;
        $hasPendingChildren = Auth::user()
            ->tasks()
            ->whereIn('id', $this->descendantIds($task))
            ->where('status', TaskStatus::Pending->value)
            ->exists();

        if ($hasPendingChildren && !$request->boolean('confirm_children')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '未完了の子タスクがあります。まとめて完了にするか確認してください。',
                    'requires_confirmation' => true,
                ], 409);
            }

            return back()->with('error', '未完了の子タスクがあります。確認してから完了してください。');
        }

        Auth::user()
            ->tasks()
            ->whereIn('id', $taskIds)
            ->where('status', TaskStatus::Pending->value)
            ->update([
                'status' => TaskStatus::Stocked->value,
                'completed_at' => $completedAt,
            ]);

        return back()->with('success', 'タスクを完了しました。');
    }

    /**
     * 未完了へ戻す
     */
    public function uncomplete(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->status !== TaskStatus::Stocked) {
            return back()->with('error', '未完了に戻せるのは討伐待ちタスクのみです。');
        }

        $taskIds = $this->descendantIds($task);
        $taskIds[] = $task->id;

        Auth::user()
            ->tasks()
            ->whereIn('id', $taskIds)
            ->where('status', TaskStatus::Stocked->value)
            ->update([
                'status' => TaskStatus::Pending->value,
                'completed_at' => null,
            ]);

        return back()->with('success', 'タスクを未完了に戻しました。');
    }

    /**
     * 削除
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        if ($task->status !== TaskStatus::Pending) {
            return back()->with('error', '削除できるのは未完了タスクのみです。');
        }

        // 子もまとめて削除（安全）
        $task->childTasks()->delete();
        $task->delete();

        return back()->with('success', 'タスクを削除しました。');
    }

    /**
     * 一括操作
     */
    public function bulk(BulkTaskRequest $request)
    {
        $data = $request->validated();

        $tasks = Auth::user()
            ->tasks()
            ->whereIn('id', $data['task_ids'])
            ->get();

        foreach ($tasks as $task) {

            if ($task->status->value !== $data['current_status']) {
                continue;
            }

            if ($data['action'] === 'complete'
                && $task->status === TaskStatus::Pending) {

                $task->update([
                    'status' => TaskStatus::Stocked,
                    'completed_at' => now(),
                ]);
            }

            if ($data['action'] === 'uncomplete'
                && $task->status === TaskStatus::Stocked) {

                $task->update([
                    'status' => TaskStatus::Pending,
                    'completed_at' => null,
                ]);
            }

            if ($data['action'] === 'delete'
                && $task->status === TaskStatus::Pending) {

                $task->childTasks()->delete();
                $task->delete();
            }
        }

        return back()->with('success', '一括操作を実行しました。');
    }

    /**
     * ボス種別判定（期間の長さ）
     */
    private function decideBossType(?string $dueDate): BossType
    {
        if (!$dueDate) {
            return BossType::Mob;
        }

        $created = now()->startOfDay();
        $due = Carbon::parse($dueDate)->startOfDay();
        $days = $created->diffInDays($due, false);

        if ($days < 0) return BossType::Mob;
        if ($days >= 30) return BossType::Boss;
        if ($days >= 7)  return BossType::MidBoss;

        return BossType::Mob;
    }

    private function descendantIds(Task $task): array
    {
        $ids = [];
        $children = Auth::user()
            ->tasks()
            ->where('parent_task_id', $task->id)
            ->get();

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }
}