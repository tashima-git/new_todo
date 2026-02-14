<?php

namespace App\Models;

use App\Enums\BossType;
use App\Enums\DueType;
use App\Enums\TaskCategory;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'difficulty',
        'category',
        'due_type',
        'due_date',
        'status',
        'stat_patience',
        'stat_speed',
        'stat_focus',
        'stat_accuracy',
        'stat_life',
        'stat_strategy',
        'parent_task_id',
        'completed_at',
    ];

    protected $casts = [
        'difficulty' => 'integer',
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

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function childTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    // --- Helpers ---

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
}
