<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ExecuteTaskKillRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ログイン必須
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            // 必要になったらここに追加
        ];
    }
}