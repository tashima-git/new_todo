<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '旅の目的を入力してください。',
            'title.max' => '旅の目的は80文字以内で入力してください。',
        ];
    }
}
