<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CreatePasswordRequest extends FormRequest
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
            'password' => 'required|min:8|max:32|confirmed',
        ];
    }

    /**
     * Get custom error messages for the validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password cannot be more than 32 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
