<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishChapterRequest;
use App\Http\Requests\StoreChapterRequest;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{
    private const STAT_LABELS = [
        'total_patience' => '忍耐',
        'total_speed' => '迅速',
        'total_focus' => '集中',
        'total_accuracy' => '正確',
        'total_life' => '生活力',
        'total_strategy' => '戦略',
    ];

    public function index()
    {
        $user = Auth::user();
        $activeChapter = $user->activeChapter()->first();
        $pastChapters = $user->chapters()
            ->whereNotNull('ended_at')
            ->latest('ended_at')
            ->get();

        return view('chapters.index', [
            'user' => $user,
            'activeChapter' => $activeChapter,
            'pastChapters' => $pastChapters,
            'statLabels' => self::STAT_LABELS,
        ]);
    }

    public function store(StoreChapterRequest $request)
    {
        $user = Auth::user();

        if ($user->activeChapter()->exists()) {
            return redirect()
                ->route('chapters.index')
                ->with('error', '進行中の旅があります。新しい旅を始めるには、現在の旅を終えてください。');
        }

        $user->chapters()->create([
            'title' => $request->validated('title'),
            'started_at' => now(),
        ]);

        return redirect()
            ->route('chapters.index')
            ->with('success', '旅の目的を設定しました。');
    }

    public function finish(FinishChapterRequest $request)
    {
        $user = Auth::user();
        $nextTitle = $request->validated('next_title');

        DB::transaction(function () use ($user, $nextTitle) {
            $activeChapter = Chapter::where('user_id', $user->id)
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->first();

            if ($activeChapter) {
                $activeChapter->update([
                    'ended_at' => now(),
                ]);
            }

            $user->chapters()->create([
                'title' => $nextTitle,
                'started_at' => now(),
            ]);
        });

        return redirect()
            ->route('chapters.index')
            ->with('success', '旅を終え、新しい旅を始めました。');
    }
}
