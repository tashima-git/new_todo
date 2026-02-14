<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可はController側で user_id を強制する
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'category' => ['required', Rule::in(['work', 'private'])],

            'due_date' => ['nullable', 'date'],

            // 1〜5
            'importance' => ['required', 'integer', 'min:1', 'max:5'],

            'is_urgent' => ['nullable', 'boolean'],

            // 0以上（上限は一旦ゆるく）
            'stat_patience' => ['required', 'integer', 'min:0'],
            'stat_speed' => ['required', 'integer', 'min:0'],
            'stat_focus' => ['required', 'integer', 'min:0'],
            'stat_accuracy' => ['required', 'integer', 'min:0'],
            'stat_life' => ['required', 'integer', 'min:0'],
            'stat_strategy' => ['required', 'integer', 'min:0'],

            // 親タスク（同一ユーザーのtaskのみ許可するのが理想）
            // MVPではControllerでチェックする
            'parent_task_id' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // checkbox未チェックだと飛んでこないので false を補う
        $this->merge([
            'is_urgent' => (bool) $this->input('is_urgent', false),
        ]);
    }
}
