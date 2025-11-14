<?php

namespace App\Http\Requests\Coach;

use Illuminate\Foundation\Http\FormRequest;

class CoachRequest extends FormRequest
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
            'user_message' => 'required|string|max:1000',
            'chat_id' => 'nullable|integer|exists:chats,id', // optional but must exist if provided
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            'user_message.required' => 'The message field cannot be empty.',
            'user_message.string' => 'The message must be a valid string.',
            'user_message.max' => 'The message cannot exceed 1000 characters.',
            'chat_id.integer' => 'The chat ID must be a valid number.',
            'chat_id.exists' => 'The specified chat does not exist.',
        ];
    }
}
