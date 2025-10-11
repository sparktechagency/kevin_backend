<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:ADMIN,USER,EMPLOYEE,MANAGER,MENTOR',
        ];
    }
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already taken.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.in' => 'The role must be one of the following: ADMIN, USER, EMPLOYEE, MANAGER, MENTOR.',
        ];
    }
}
