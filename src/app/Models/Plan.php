<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'max_daily',
        'max_weekly',
        'max_monthly',
        'max_task_changes',
    ];

    protected $casts = [
        'max_daily' => 'integer',
        'max_weekly' => 'integer',
        'max_monthly' => 'integer',
        'max_task_changes' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
