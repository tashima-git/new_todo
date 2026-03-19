<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|in:work,private',
            'due_date' => 'nullable|date|after_or_equal:today',
            'importance' => 'required|integer|min:1|max:5',
            'is_urgent' => 'boolean',
            'parent_task_id' => 'nullable|integer',

            'stat_patience' => 'required|integer|min:0',
            'stat_speed' => 'required|integer|min:0',
            'stat_focus' => 'required|integer|min:0',
            'stat_accuracy' => 'required|integer|min:0',
            'stat_life' => 'required|integer|min:0',
            'stat_strategy' => 'required|integer|min:0',
        ];
    }
}