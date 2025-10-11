<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'otp' => 'required|numeric',
            'email' => 'required|email|exists:users,email',
        ];
    }
    public function messages()
    {
        return [
            'otp.required' => 'The OTP is required.',
            'otp.numeric' => 'The OTP must be a numeric value.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.exists' => 'The provided email does not exist in our records.',
        ];
    }
}
