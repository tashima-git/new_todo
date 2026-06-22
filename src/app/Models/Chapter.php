<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    public const STAT_FIELDS = [
        'total_patience',
        'total_speed',
        'total_focus',
        'total_accuracy',
        'total_life',
        'total_strategy',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'started_at',
        'ended_at',
        'total_patience',
        'total_speed',
        'total_focus',
        'total_accuracy',
        'total_life',
        'total_strategy',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'total_patience' => 'integer',
        'total_speed' => 'integer',
        'total_focus' => 'integer',
        'total_accuracy' => 'integer',
        'total_life' => 'integer',
        'total_strategy' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
