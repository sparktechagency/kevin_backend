<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,company_email',
            'company_phone' => 'required|string|max:15',  // Assuming phone number is a string
            'company_address' => 'nullable|string|max:500',  // Optional field
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Optional logo field, max size 2MB
            'manager_full_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:companies,manager_email',
            'manager_phone' => 'required|string|max:15',
            'manager_code' => 'nullable|string|unique:companies,manager_code', // Auto-generated code
            'send_welcome_email' => 'nullable|boolean',  // Default is true, can be updated with 0 or 1
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages()
    {
        return [
            'company_name.required' => 'The company name is required.',
            'company_email.unique' => 'The company email has already been taken.',
            'manager_email.unique' => 'The manager email has already been taken.',
            'manager_code.unique' => 'The manager code must be unique.',
        ];
    }
}
