<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'next_title' => ['required', 'string', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'next_title.required' => '次の旅の目的を入力してください。',
            'next_title.max' => '次の旅の目的は80文字以内で入力してください。',
        ];
    }
}
