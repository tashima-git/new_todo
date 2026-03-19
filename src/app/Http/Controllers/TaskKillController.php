<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteTaskKillRequest;
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
    // 1体討伐（API）
    // ==============================
    public function execute(ExecuteTaskKillRequest $request)
    {
        $user = Auth::user();
        $taskId = $request->task_id;

        $result = DB::transaction(function () use ($taskId, $user) {

            // ★ トランザクション内でロック
            $task = Task::where('user_id', $user->id)
                ->where('status', 'stocked')
                ->where('id', $taskId)
                ->lockForUpdate()
                ->first();

            if (!$task) {
                abort(404, 'タスクが見つかりません');
            }

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

            // ユーザーへステータス加算
            $user->increment('total_patience',  $task->stat_patience);
            $user->increment('total_speed',     $task->stat_speed);
            $user->increment('total_focus',     $task->stat_focus);
            $user->increment('total_accuracy',  $task->stat_accuracy);
            $user->increment('total_life',      $task->stat_life);
            $user->increment('total_strategy',  $task->stat_strategy);

            // セッションへ今回のログIDを追加
            $existing = session()->get('taskkill_log_ids', []);
            $existing[] = $log->id;
            session()->put('taskkill_log_ids', $existing);

            // タスク削除
            $task->delete();

            return [
                'log_id' => $log->id,
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