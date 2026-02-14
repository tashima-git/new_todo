<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * 未完了タスク一覧（pending / stocked）
     */
    public function index(Request $request)
    {
        // ページネーション対応: 1ページ10件
        $tasks = Auth::user()
            ->tasks()
            ->whereIn('status', ['pending', 'stocked'])
            ->orderBy('due_date')
            ->paginate(10)
            ->withQueryString(); // ?status=stocked なども保持

        return view('tasks.index', compact('tasks'));
    }

    /**
     * タスク作成画面
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * タスク保存
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:work,private',
            'due_date' => 'nullable|date',
            'importance' => 'integer|min:1|max:5',
            'is_urgent' => 'boolean',
            'stat_patience' => 'integer|min:0',
            'stat_speed' => 'integer|min:0',
            'stat_focus' => 'integer|min:0',
            'stat_accuracy' => 'integer|min:0',
            'stat_life' => 'integer|min:0',
            'stat_strategy' => 'integer|min:0',
        ]);

        Auth::user()->tasks()->create($data);

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました。');
    }

    /**
     * タスク詳細
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    /**
     * 編集画面
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:work,private',
            'due_date' => 'nullable|date',
            'importance' => 'integer|min:1|max:5',
            'is_urgent' => 'boolean',
            'stat_patience' => 'integer|min:0',
            'stat_speed' => 'integer|min:0',
            'stat_focus' => 'integer|min:0',
            'stat_accuracy' => 'integer|min:0',
            'stat_life' => 'integer|min:0',
            'stat_strategy' => 'integer|min:0',
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました。');
    }

    /**
     * タスク完了
     */
    public function complete(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => 'stocked',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'タスクを完了しました。');
    }

    /**
     * タスク未完了に戻す
     */
    public function uncomplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        return back()->with('success', 'タスクを未完了に戻しました。');
    }

    /**
     * 一括操作
     */
    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $taskIds = $request->input('task_ids', []);

        $tasks = Auth::user()->tasks()->whereIn('id', $taskIds)->get();

        foreach ($tasks as $task) {
            if ($action === 'complete' && in_array($task->status, ['pending', 'stocked'])) {
                $task->update(['status' => 'stocked', 'completed_at' => now()]);
            } elseif ($action === 'uncomplete' && $task->status === 'stocked') {
                $task->update(['status' => 'pending', 'completed_at' => null]);
            }
        }

        return back()->with('success', '一括操作を実行しました。');
    }

    /**
     * タスク削除（pendingのみ許可）
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        if ($task->status !== 'pending') {
            return back()->with('error', '未完了タスクのみ削除できます。');
        }

        $task->delete();

        return back()->with('success', 'タスクを削除しました。');
    }
}
