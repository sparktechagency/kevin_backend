<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
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
            'current_password' => 'required',
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
            'current_password.required' => 'The current password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password cannot be more than 32 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
