<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskKillLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_title',
        'task_created_at',
        'task_completed_at',
        'user_id',
        'boss_type',
        'gained_patience',
        'gained_speed',
        'gained_focus',
        'gained_accuracy',
        'gained_life',
        'gained_strategy',
    ];

    protected $casts = [
        'task_created_at' => 'datetime',
        'task_completed_at' => 'datetime',
    ];

    // 所有者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
