<?php

namespace App\Models;

use App\Enums\BossType;
use App\Enums\DueType;
use App\Enums\TaskCategory;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',

        // タスク種別
        'difficulty',
        'boss_type',

        // 分類
        'category',
        'due_type',
        'due_date',
        'importance',
        'is_urgent',
        'status',

        // ステータス
        'stat_patience',
        'stat_speed',
        'stat_focus',
        'stat_accuracy',
        'stat_life',
        'stat_strategy',

        // 親子
        'parent_task_id',

        // 完了情報
        'completed_at',
    ];

    protected $casts = [
        'difficulty' => 'string',
        'boss_type' => BossType::class,
        'is_urgent' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'category' => TaskCategory::class,
        'due_type' => DueType::class,
        'status' => TaskStatus::class,
    ];

    // --- Relations ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function killLogs(): HasMany
    {
        return $this->hasMany(TaskKillLog::class);
    }

    // 親タスク
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    // 子タスク
    public function childTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    // --- Helpers ---

    /**
     * 合計ステータス値を返す
     */
    public function totalAssignedStats(): int
    {
        return
            $this->stat_patience +
            $this->stat_speed +
            $this->stat_focus +
            $this->stat_accuracy +
            $this->stat_life +
            $this->stat_strategy;
    }

    /**
     * 子タスク・孫タスクまで含めて階層を取得
     * Bladeでのアコーディオン表示に便利
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }
}
