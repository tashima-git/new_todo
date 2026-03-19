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
        // 今回は入力値なしだが、将来拡張用に空で定義
        return [
            // 例: 'confirm' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // 必要になったらここに追加
        ];
    }
}