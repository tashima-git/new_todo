<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tips' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'tips.required' => 'Tipsは必須です。',
            'tips.max' => 'Tipsは255文字以内で入力してください。',
        ];
    }
}