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
    $status   = $request->input('status', 'pending');
    $category = $request->input('category');
    $bossType = $request->input('boss_type');
    $sortDue  = $request->boolean('sort_due', false);
    $view     = $request->input('view', 'tree'); // ★ 追加

    $query = Auth::user()->tasks()
        ->where(
            'status',
            $status === 'stocked'
                ? TaskStatus::Stocked
                : TaskStatus::Pending
        );

    /*
    |--------------------------------------------------------------------------
    | 表示モード制御（最重要）
    |--------------------------------------------------------------------------
    */

    if ($view === 'tree' && $status === 'pending') {

        // ツリー表示（今まで通り）
        $query->whereNull('parent_task_id')
              ->with(['childTasks' => function ($q) {
                  $q->with('childTasks');
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

    $tasks = $query->paginate(10)->withQueryString();

    return view('tasks.index', compact(
        'tasks',
        'status',
        'sortDue',
        'view' // ★ 追加
    ));
}

    /**
     * 作成画面
     */
    public function create(Request $request)
    {
        $parentTaskId = $request->query('parent_task_id');
        $parentTask = null;

        if ($parentTaskId) {
            $parentTask = Auth::user()->tasks()->find($parentTaskId);
        }

        return view('tasks.create', compact('parentTaskId', 'parentTask'));
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
        return view('tasks.edit', compact('task'));
    }

    /**
     * 更新
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

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
    public function complete(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => TaskStatus::Stocked,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'タスクを完了しました。');
    }

    /**
     * 未完了へ戻す
     */
    public function uncomplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => TaskStatus::Pending,
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
}