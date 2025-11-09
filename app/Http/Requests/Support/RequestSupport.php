<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class RequestSupport extends FormRequest
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
            'content' => 'required|string|max:10000',
        ];
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Please enter the content.',
            'content.string'   => 'The content must be a valid text.',
            'content.max'      => 'The content cannot exceed 10,000 characters.',
        ];
    }

}
