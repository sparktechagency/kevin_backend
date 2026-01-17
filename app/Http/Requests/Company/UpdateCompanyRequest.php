<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
   public function rules(): array
    {
        $companyId = $this->route('id');

        return [
            'company_name' => 'required|string|max:255',

            'company_email' => [
                'required',
                'email',
                Rule::unique('companies', 'company_email')->ignore($companyId),
            ],

            'company_phone' => 'required|string|max:15',
            'company_address' => 'nullable|string|max:500',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'manager_full_name' => 'required|string|max:255',

            'manager_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($companyId),
            ],

            'manager_phone' => 'required|string|max:15',

            'password' => 'nullable|string|min:8|max:16',
            'send_welcome_email' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.string'   => 'Company name must be a valid string.',
            'company_name.max'      => 'Company name may not exceed 255 characters.',

            'company_email.required' => 'Company email is required.',
            'company_email.email'    => 'Please provide a valid company email address.',
            'company_email.unique'   => 'This company email is already in use.',

            'company_phone.required' => 'Company phone number is required.',
            'company_phone.string'   => 'Company phone number must be valid.',
            'company_phone.max'      => 'Company phone number may not exceed 15 characters.',

            'company_address.string' => 'Company address must be a valid string.',
            'company_address.max'    => 'Company address may not exceed 500 characters.',

            'company_logo.image' => 'Company logo must be an image file.',
            'company_logo.mimes' => 'Logo must be jpeg, png, jpg, gif, or svg.',
            'company_logo.max'   => 'Logo size must not exceed 2MB.',

            'manager_full_name.required' => 'Manager full name is required.',
            'manager_full_name.string'   => 'Manager full name must be valid.',
            'manager_full_name.max'      => 'Manager full name may not exceed 255 characters.',

            'manager_email.required' => 'Manager email is required.',
            'manager_email.email'    => 'Please provide a valid manager email.',
            'manager_email.unique'   => 'This manager email is already assigned to another company.',

            'manager_phone.required' => 'Manager phone number is required.',
            'manager_phone.string'   => 'Manager phone number must be valid.',
            'manager_phone.max'      => 'Manager phone number may not exceed 15 characters.',

            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 16 characters.',

            'send_welcome_email.boolean' => 'Send welcome email must be true or false.',
        ];
    }

}
