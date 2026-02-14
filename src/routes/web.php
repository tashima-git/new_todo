<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskKillController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\HelpController;

// -------------------------------
// TOP（ログイン必須にするなら auth を付ける）
// -------------------------------
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// -------------------------------
// Help (guest OK)
// -------------------------------
Route::get('/help', [HelpController::class, 'index'])->name('help.index');

// -------------------------------
// Auth required
// -------------------------------
Route::middleware(['auth'])->group(function () {

    // -------------------------------
    // Tasks
    // -------------------------------
    Route::prefix('tasks')->name('tasks.')->group(function () {

        Route::get('/', [TaskController::class, 'index'])->name('index');

        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');

        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');

        // 完了・未完了
        Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
        Route::patch('/{task}/uncomplete', [TaskController::class, 'uncomplete'])->name('uncomplete');

        // 一括操作
        Route::post('/bulk', [TaskController::class, 'bulk'])->name('bulk');

        // 削除（pendingのみ許可）
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });

    // -------------------------------
    // TaskKill
    // -------------------------------
    Route::get('/taskkill', [TaskKillController::class, 'index'])->name('taskkill.index');
    Route::post('/taskkill/execute', [TaskKillController::class, 'execute'])->name('taskkill.execute');
    Route::get('/taskkill/result', [TaskKillController::class, 'result'])->name('taskkill.result');

    // -------------------------------
    // Stats / Status
    // -------------------------------
    Route::get('/stats', [StatController::class, 'index'])->name('stats.index');
    Route::get('/status', [StatusController::class, 'index'])->name('status.index');

    // -------------------------------
    // Achievements
    // -------------------------------
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');

    // -------------------------------
    // Plan
    // -------------------------------
    Route::get('/plan', [PlanController::class, 'index'])->name('plan.index');
    Route::post('/plan', [PlanController::class, 'update'])->name('plan.update');
});
