<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'settings.se_volume' => ['required', 'integer', 'min:0', 'max:100'],
            'settings.taskkill_se_volume' => ['required', 'integer', 'min:0', 'max:100'],
            'settings.status_se_volume' => ['required', 'integer', 'min:0', 'max:100'],
            'settings.voice_type' => ['required', Rule::in(['none', 'guide', 'samurai', 'partner'])],
            'settings.voice_volume' => ['required', 'integer', 'min:0', 'max:100'],
            'settings.default_task_view' => ['required', Rule::in(['tree', 'flat'])],
            'settings.confirm_important_actions' => ['required', 'boolean'],
            'settings.deadline_notification_enabled' => ['required', 'boolean'],
            'settings.deadline_notification_timing' => ['required', Rule::in(['same_day', 'one_day_before', 'three_days_before'])],
            'settings.tasks_per_page' => ['required', 'integer', Rule::in([10, 20, 50])],
            'settings.auto_strategy_on_create' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '表示名を入力してください。',
            'name.max' => '表示名は20文字以内で入力してください。',
            'settings.*.required' => '設定値が不足しています。',
            'settings.*.integer' => '設定値が不正です。',
            'settings.*.in' => '設定値が不正です。',
            'settings.*.min' => '設定値が不正です。',
            'settings.*.max' => '設定値が不正です。',
        ];
    }
}
