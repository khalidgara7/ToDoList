<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'status' => 'sometimes|nullable|in:pending,in_progress,completed',
            'assigned_to' => 'sometimes|nullable|integer|exists:users,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title may not be greater than 255 characters.',
            'description.max' => 'Task description may not be greater than 1000 characters.',
            'status.in' => 'Status must be one of: pending, in_progress, completed.',
            'assigned_to.integer' => 'Assigned user must be a valid user ID.',
            'assigned_to.exists' => 'The selected user does not exist.',
        ];
    }
}