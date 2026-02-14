<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Achievement;
use App\Models\UserAchievement;

class AchievementController extends Controller
{
    /**
     * 実績一覧
     */
    public function index()
    {
        $user = Auth::user();

        // achievements マスタ全件
        $achievements = Achievement::query()
            ->orderBy('id', 'asc')
            ->get();

        // ユーザーの解除状況をまとめて取得（N+1防止）
        $userAchievements = UserAchievement::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');

        // Bladeで扱いやすいように unlocked_at を付与
        foreach ($achievements as $achievement) {
            $ua = $userAchievements->get($achievement->id);

            // unlocked_at が無い場合は null
            $achievement->unlocked_at = $ua?->unlocked_at;
        }

        // 解除済みを上に並べ替え（解除日が新しい順）
        $achievements = $achievements->sortByDesc(function ($achievement) {
            return $achievement->unlocked_at ? strtotime($achievement->unlocked_at) : 0;
        })->values();

        // 解除数
        $unlockedCount = $achievements->filter(function ($achievement) {
            return !empty($achievement->unlocked_at);
        })->count();

        return view('achievements.index', [
            'achievements'  => $achievements,
            'unlockedCount' => $unlockedCount,
            'totalCount'    => $achievements->count(),
        ]);
    }
}
