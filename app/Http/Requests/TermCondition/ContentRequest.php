<?php

namespace App\Http\Requests\TermCondition;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
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
            'content' => 'required|string|min:10|max:100000',
        ];
    }

    /**
     * Custom messages for validation errors (optional)
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Content cannot be empty.',
            'content.min'      => 'Content must be at least 10 characters.',
            'content.max'      => 'Content cannot exceed 100000 characters.',
        ];
    }
}
