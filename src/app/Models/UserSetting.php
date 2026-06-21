<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    public const DEFAULTS = [
        'se_volume' => 50,
        'taskkill_se_volume' => 50,
        'status_se_volume' => 50,
        'voice_type' => 'none',
        'voice_volume' => 50,
        'default_task_view' => 'tree',
        'confirm_important_actions' => true,
        'deadline_notification_enabled' => false,
        'deadline_notification_timing' => 'same_day',
        'tasks_per_page' => 10,
        'auto_strategy_on_create' => 1,
    ];

    protected $fillable = [
        'user_id',
        'se_volume',
        'taskkill_se_volume',
        'status_se_volume',
        'voice_type',
        'voice_volume',
        'default_task_view',
        'confirm_important_actions',
        'deadline_notification_enabled',
        'deadline_notification_timing',
        'tasks_per_page',
        'auto_strategy_on_create',
    ];

    protected $casts = [
        'se_volume' => 'integer',
        'taskkill_se_volume' => 'integer',
        'status_se_volume' => 'integer',
        'voice_volume' => 'integer',
        'confirm_important_actions' => 'boolean',
        'deadline_notification_enabled' => 'boolean',
        'tasks_per_page' => 'integer',
        'auto_strategy_on_create' => 'integer',
    ];

    public static function defaultValues(): array
    {
        return self::DEFAULTS;
    }

    public function toFormValues(): array
    {
        return array_merge(self::DEFAULTS, $this->only(array_keys(self::DEFAULTS)));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
