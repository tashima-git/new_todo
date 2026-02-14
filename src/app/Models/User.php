<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',

        // 合計ステ
        'total_patience',
        'total_speed',
        'total_focus',
        'total_accuracy',
        'total_life',
        'total_strategy',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',

        'total_patience' => 'integer',
        'total_speed' => 'integer',
        'total_focus' => 'integer',
        'total_accuracy' => 'integer',
        'total_life' => 'integer',
        'total_strategy' => 'integer',
    ];

    // --- Relations ---

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function killLogs()
    {
        return $this->hasMany(TaskKillLog::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['unlocked_at'])
            ->withTimestamps();
    }

    public function userMessageSettings()
    {
        return $this->hasMany(UserMessageSetting::class);
    }

    public function userCustomMessages()
    {
        return $this->hasMany(UserCustomMessage::class);
    }
}
