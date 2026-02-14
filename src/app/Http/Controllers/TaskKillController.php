<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskKillLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskKillController extends Controller
{
    /**
     * 討伐待ち一覧
     */
    public function index()
    {
        $user = Auth::user();

        // stocked = 討伐待ち
        $tasks = Task::query()
            ->where('user_id', $user->id)
            ->where('status', 'stocked')
            ->orderBy('completed_at', 'asc')
            ->get();

        // 表示用：合計獲得ステ（演出にも使える）
        $sumStats = [
            'patience' => $tasks->sum('stat_patience'),
            'speed' => $tasks->sum('stat_speed'),
            'focus' => $tasks->sum('stat_focus'),
            'accuracy' => $tasks->sum('stat_accuracy'),
            'life' => $tasks->sum('stat_life'),
            'strategy' => $tasks->sum('stat_strategy'),
        ];

        return view('taskkill.index', compact('tasks', 'sumStats'));
    }

    /**
     * 討伐実行（stocked を全て討伐）
     * ※ここがログ保存処理の本体
     */
    public function execute(Request $request)
    {
        $user = Auth::user();

        // stocked のみ対象
        $tasks = Task::query()
            ->where('user_id', $user->id)
            ->where('status', 'stocked')
            ->orderBy('completed_at', 'asc')
            ->get();

        if ($tasks->isEmpty()) {
            return redirect()
                ->route('taskkill.index')
                ->with('error', '討伐待ちのタスクがありません。');
        }

        // 結果画面に渡す用
        $result = [
            'killed_count' => 0,
            'killed_tasks' => [],
            'gained' => [
                'patience' => 0,
                'speed' => 0,
                'focus' => 0,
                'accuracy' => 0,
                'life' => 0,
                'strategy' => 0,
            ],
        ];

        DB::transaction(function () use ($tasks, $user, &$result) {

            foreach ($tasks as $task) {

                // ボスタイプ判定（あなたのルール：期間の長さで）
                // ※due_date が無い場合は「mob」に寄せる
                $bossType = $this->decideBossType($task);

                // ログ保存（軽量）
                TaskKillLog::create([
                    'task_title' => $task->title,
                    'task_created_at' => $task->created_at,
                    'task_completed_at' => $task->completed_at ?? now(),
                    'user_id' => $user->id,
                    'boss_type' => $bossType,
                    'gained_patience' => $task->stat_patience,
                    'gained_speed' => $task->stat_speed,
                    'gained_focus' => $task->stat_focus,
                    'gained_accuracy' => $task->stat_accuracy,
                    'gained_life' => $task->stat_life,
                    'gained_strategy' => $task->stat_strategy,
                ]);

                // ユーザーの累計ステに加算
                $user->total_patience += $task->stat_patience;
                $user->total_speed += $task->stat_speed;
                $user->total_focus += $task->stat_focus;
                $user->total_accuracy += $task->stat_accuracy;
                $user->total_life += $task->stat_life;
                $user->total_strategy += $task->stat_strategy;

                // 結果用
                $result['killed_count']++;
                $result['killed_tasks'][] = [
                    'title' => $task->title,
                    'boss_type' => $bossType,
                    'completed_at' => $task->completed_at,
                    'created_at' => $task->created_at,
                ];

                $result['gained']['patience'] += $task->stat_patience;
                $result['gained']['speed'] += $task->stat_speed;
                $result['gained']['focus'] += $task->stat_focus;
                $result['gained']['accuracy'] += $task->stat_accuracy;
                $result['gained']['life'] += $task->stat_life;
                $result['gained']['strategy'] += $task->stat_strategy;

                // tasksから削除（完全討伐）
                $task->delete();
            }

            // ユーザー更新
            $user->save();
        });

        // 結果画面に渡す（DBに残さず軽量に）
        session()->put('taskkill_result', $result);

        return redirect()->route('taskkill.result');
    }

    /**
     * 討伐結果画面
     */
    public function result()
    {
        $result = session()->get('taskkill_result');

        if (!$result) {
            return redirect()
                ->route('taskkill.index')
                ->with('error', '討伐結果が見つかりませんでした。');
        }

        return view('taskkill.result', compact('result'));
    }

    /**
     * ボスタイプ判定
     * ルール：
     * - 期限なし：mob
     * - due_date が 30日以上先：boss
     * - due_date が 7日以上先：mid
     * - それ以外：mob
     *
     * ※これは後で調整しやすいように1箇所に隔離してある
     */
    private function decideBossType(Task $task): string
    {
        if (!$task->due_date) {
            return 'mob';
        }

        $days = now()->startOfDay()->diffInDays($task->due_date, false);

        // 期限が過去でも「mob」扱いにして暴れないようにする
        if ($days < 0) {
            return 'mob';
        }

        if ($days >= 30) {
            return 'boss';
        }

        if ($days >= 7) {
            return 'mid';
        }

        return 'mob';
    }
}
