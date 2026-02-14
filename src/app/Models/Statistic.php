<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistic extends Model
{
    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'total_kills',
        'kills_by_difficulty',
        'kills_by_category',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_kills' => 'integer',
        'kills_by_difficulty' => 'array',
        'kills_by_category' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
