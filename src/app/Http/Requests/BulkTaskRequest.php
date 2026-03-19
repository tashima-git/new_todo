<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:complete,uncomplete,delete',
            'task_ids' => 'required|array',
            'task_ids.*' => 'integer',
            'current_status' => 'required|in:pending,stocked',
        ];
    }
}