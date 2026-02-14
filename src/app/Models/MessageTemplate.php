<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageTemplate extends Model
{
    protected $fillable = [
        'type',
        'is_default',
        'content',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserMessageSetting::class);
    }
}
