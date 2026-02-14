<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskKillLog;

class StatController extends Controller
{
    /**
     * 戦歴（統計）画面
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // -----------------------------
        // 集計（軽量）
        // -----------------------------
        $totalKills = TaskKillLog::where('user_id', $user->id)->count();

        $killsByType = TaskKillLog::where('user_id', $user->id)
            ->selectRaw('boss_type, COUNT(*) as cnt')
            ->groupBy('boss_type')
            ->pluck('cnt', 'boss_type')
            ->toArray();

        $summary = [
            'total_kills' => $totalKills,
            'mob' => $killsByType['mob'] ?? 0,
            'mid' => $killsByType['mid'] ?? 0,
            'boss' => $killsByType['boss'] ?? 0,
        ];

        // -----------------------------
        // 最近の討伐ログ（最新20件）
        // -----------------------------
        $recentLogs = TaskKillLog::where('user_id', $user->id)
            ->orderByDesc('task_completed_at')
            ->limit(20)
            ->get();

        return view('stats.index', compact('summary', 'recentLogs'));
    }
}
