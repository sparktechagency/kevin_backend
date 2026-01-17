<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'       => 'required|string|max:255',
            'company_email'      => 'required|email|unique:companies,company_email',
            'company_phone'      => 'required|string|max:15',
            'company_address'    => 'nullable|string|max:500',
            'company_logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'manager_full_name'  => 'required|string|max:255',
            'manager_email'      => 'required|email|unique:users,email',
            'manager_phone'      => 'required|string|max:15',
            'password'            => 'nullable|min:8|max:16',
            'send_welcome_email' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required'  => 'The company name is required.',
            'company_email.unique'   => 'The company email has already been taken.',
            'manager_email.unique'   => 'The manager email has already been taken.',
            'manager_code.unique'    => 'The manager code must be unique.',
            'password.min'            => 'Password must be at least 8 characters.',
            'password.max'            => 'Password may not exceed 16 characters.',
        ];
    }
}
