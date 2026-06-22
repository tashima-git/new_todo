<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteTaskKillRequest;
use App\Enums\TaskStatus;
use App\Models\Chapter;
use App\Models\Task;
use App\Models\TaskKillLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskKillController extends Controller
{
    // ==============================
    // 討伐画面
    // ==============================
    public function index()
    {
        session()->forget('taskkill_log_ids');

        $user = Auth::user();

        $tasks = Task::where('user_id', $user->id)
            ->where('status', 'stocked')
            ->orderBy('completed_at', 'asc')
            ->get();

        return view('taskkill.index', compact('tasks'));
    }

    // ==============================
    // 討伐確定（API）
    // ==============================
    public function execute(ExecuteTaskKillRequest $request)
    {
        $user = Auth::user();
        $taskIds = $request->validated('task_ids');

        $result = DB::transaction(function () use ($taskIds, $user) {

            // 演出開始前に、今回の討伐対象をまとめて確定する。
            $tasks = Task::where('user_id', $user->id)
                ->where('status', TaskStatus::Stocked->value)
                ->whereIn('id', $taskIds)
                ->orderBy('completed_at', 'asc')
                ->lockForUpdate()
                ->get();

            if ($tasks->isEmpty()) {
                abort(404, '討伐待ちタスクが見つかりません');
            }

            if ($tasks->count() !== count(array_unique($taskIds))) {
                abort(422, '討伐対象にできないタスクが含まれています');
            }

            $logIds = [];
            $gained = [
                'total_patience' => 0,
                'total_speed' => 0,
                'total_focus' => 0,
                'total_accuracy' => 0,
                'total_life' => 0,
                'total_strategy' => 0,
            ];

            foreach ($tasks as $task) {
                $bossType = $this->decideBossType($task);

                $log = TaskKillLog::create([
                    'task_id'            => $task->id,
                    'task_title'         => $task->title,
                    'task_created_at'    => $task->created_at,
                    'task_completed_at'  => $task->completed_at ?? now(),
                    'user_id'            => $user->id,
                    'boss_type'          => $bossType,
                    'gained_patience'    => $task->stat_patience,
                    'gained_speed'       => $task->stat_speed,
                    'gained_focus'       => $task->stat_focus,
                    'gained_accuracy'    => $task->stat_accuracy,
                    'gained_life'        => $task->stat_life,
                    'gained_strategy'    => $task->stat_strategy,
                ]);

                $logIds[] = $log->id;

                $gained['total_patience'] += $task->stat_patience;
                $gained['total_speed'] += $task->stat_speed;
                $gained['total_focus'] += $task->stat_focus;
                $gained['total_accuracy'] += $task->stat_accuracy;
                $gained['total_life'] += $task->stat_life;
                $gained['total_strategy'] += $task->stat_strategy;

                $task->update([
                    'status' => TaskStatus::Killed,
                ]);
            }

            $user->increment('total_patience', $gained['total_patience']);
            $user->increment('total_speed', $gained['total_speed']);
            $user->increment('total_focus', $gained['total_focus']);
            $user->increment('total_accuracy', $gained['total_accuracy']);
            $user->increment('total_life', $gained['total_life']);
            $user->increment('total_strategy', $gained['total_strategy']);

            $chapter = Chapter::where('user_id', $user->id)
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->first();

            if (!$chapter) {
                $chapter = $user->chapters()->create([
                    'title' => '未設定の旅',
                    'started_at' => now(),
                ]);
            }

            $chapter->increment('total_patience', $gained['total_patience']);
            $chapter->increment('total_speed', $gained['total_speed']);
            $chapter->increment('total_focus', $gained['total_focus']);
            $chapter->increment('total_accuracy', $gained['total_accuracy']);
            $chapter->increment('total_life', $gained['total_life']);
            $chapter->increment('total_strategy', $gained['total_strategy']);

            session()->put('taskkill_log_ids', $logIds);

            return [
                'log_ids' => $logIds,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    // ==============================
    // 討伐結果画面
    // ==============================
    public function result()
    {
        $user = Auth::user();

        // 今回分だけ取得して即消す
        $logIds = session()->pull('taskkill_log_ids', []);

        if (empty($logIds)) {
            return redirect()
                ->route('taskkill.index')
                ->with('error', '討伐結果が見つかりませんでした。');
        }

        $logs = TaskKillLog::where('user_id', $user->id)
            ->whereIn('id', $logIds)
            ->orderBy('id', 'asc')
            ->get()
            ->values(); // ★ インデックスを正規化

        if ($logs->isEmpty()) {
            return redirect()
                ->route('taskkill.index')
                ->with('error', '討伐結果が見つかりませんでした。');
        }

        $totalGained = [
            'patience'  => $logs->sum('gained_patience'),
            'speed'     => $logs->sum('gained_speed'),
            'focus'     => $logs->sum('gained_focus'),
            'accuracy'  => $logs->sum('gained_accuracy'),
            'life'      => $logs->sum('gained_life'),
            'strategy'  => $logs->sum('gained_strategy'),
        ];

        return view('taskkill.result', compact('logs', 'totalGained'));
    }

    // ==============================
    // ボス判定
    // ==============================
    private function decideBossType(Task $task): string
    {
        if (!$task->due_date) {
            return 'mob';
        }

        $days = $task->created_at->diffInDays($task->due_date);

        if ($days >= 30) return 'boss';
        if ($days >= 7)  return 'mid';

        return 'mob';
    }
}