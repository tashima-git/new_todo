<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    private const STAT_FIELDS = [
        'stat_patience',
        'stat_speed',
        'stat_focus',
        'stat_accuracy',
        'stat_life',
        'stat_strategy',
    ];

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'memo' => 'nullable|string|max:2000',
            'category' => 'required|in:work,private',
            'due_date' => 'nullable|date|after_or_equal:today',
            'importance' => 'required|integer|min:1|max:5',
            'is_urgent' => 'boolean',

            'stat_patience' => 'required|integer|min:0|max:6',
            'stat_speed' => 'required|integer|min:0|max:6',
            'stat_focus' => 'required|integer|min:0|max:6',
            'stat_accuracy' => 'required|integer|min:0|max:6',
            'stat_life' => 'required|integer|min:0|max:6',
            'stat_strategy' => 'required|integer|min:0|max:6',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $total = collect(self::STAT_FIELDS)
                ->sum(fn ($field) => (int) $this->input($field, 0));

            if ($total > 6) {
                $validator->errors()->add('stats_total', 'ステータス割り振りは合計6以内にしてください。');
            }
        });
    }
}