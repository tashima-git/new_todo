<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:255'],
            'is_completed' => ['boolean'],
            'tips' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Todoの内容を入力してください',
            'content.max' => 'Todoの内容は255文字以内で入力してください',
            'is_completed.boolean' => '完了ステータスは真偽値で指定してください',
            'tips.max' => 'ヒントは255文字以内で入力してください',
        ];
    }
}