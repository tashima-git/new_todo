<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DevToolController extends Controller
{
    public function generateTasks()
    {
        if (!app()->environment('local')) {
            abort(403);
        }

        $user = Auth::user();

        $statuses = ['pending', 'stocked', 'killed'];
        $categories = ['work', 'private'];

for ($i = 1; $i <= 10; $i++) {

    $stats = [
        'stat_patience' => 0,
        'stat_speed' => 0,
        'stat_focus' => 0,
        'stat_accuracy' => 0,
        'stat_life' => 0,
        'stat_strategy' => 0,
    ];

    $keys = array_keys($stats);

    for ($p = 0; $p < 6; $p++) {
        $key = $keys[array_rand($keys)];
        $stats[$key]++;
    }

    // タイトル番号と期限を一致させる
    $dueDate = Carbon::today()->addDays($i);

    Task::create([
        'user_id' => $user->id,
        'title' => 'テストタスク ' . $i,
        'category' => $categories[array_rand($categories)],
        'status' => 'pending',
        'importance' => rand(1,5),
        'is_urgent' => rand(0,1),
        'boss_type' => 'mob',
        'due_date' => $dueDate,

        ...$stats
    ]);
}

        return back()->with('success', 'テストタスクを10件生成しました');
    }
}