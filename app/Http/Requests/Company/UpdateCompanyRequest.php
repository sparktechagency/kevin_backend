<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
        // Dynamically fetch the 'id' from the route
        $id = $this->route('id');

        return [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,company_email,' . $id,  // Unique check except for current company
            'company_phone' => 'required|string|max:15',
            'company_address' => 'nullable|string|max:500',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate image logo (optional)
            'manager_full_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:companies,manager_email,' . $id,  // Unique check except for current company
            'manager_phone' => 'required|string|max:15',
            'manager_code' => 'nullable|string|unique:companies,manager_code,' . $id,  // Unique check except for current company
            'send_welcome_email' => 'nullable|boolean',  // Default value can be handled in the model
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages()
    {
        return [
            'company_name.required' => 'The company name is required.',
            'company_name.string' => 'The company name must be a valid string.',
            'company_name.max' => 'The company name cannot exceed 255 characters.',
            'company_email.required' => 'The company email is required.',
            'company_email.email' => 'Please provide a valid company email address.',
            'company_email.unique' => 'The company email has already been taken.',
            'company_phone.required' => 'The company phone number is required.',
            'company_phone.string' => 'The company phone number must be a valid string.',
            'company_phone.max' => 'The company phone number cannot exceed 15 characters.',
            'company_address.string' => 'The company address must be a valid string.',
            'company_address.max' => 'The company address cannot exceed 500 characters.',
            'company_logo.image' => 'The company logo must be a valid image.',
            'company_logo.mimes' => 'The company logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'company_logo.max' => 'The company logo cannot exceed 2MB.',
            'manager_full_name.required' => 'The manager full name is required.',
            'manager_full_name.string' => 'The manager full name must be a valid string.',
            'manager_full_name.max' => 'The manager full name cannot exceed 255 characters.',
            'manager_email.required' => 'The manager email is required.',
            'manager_email.email' => 'Please provide a valid manager email address.',
            'manager_email.unique' => 'The manager email has already been taken.',
            'manager_phone.required' => 'The manager phone number is required.',
            'manager_phone.string' => 'The manager phone number must be a valid string.',
            'manager_phone.max' => 'The manager phone number cannot exceed 15 characters.',
            'manager_code.unique' => 'The manager code must be unique.',
            'send_welcome_email.boolean' => 'The send_welcome_email field must be true or false.',
        ];
    }
}
