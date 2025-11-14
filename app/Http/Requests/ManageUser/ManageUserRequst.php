<?php

namespace App\Http\Requests\ManageUser;

use Illuminate\Foundation\Http\FormRequest;

class ManageUserRequst extends FormRequest
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
                'name'               => 'required|string|min:2|max:255',
                'email'              => 'required|email|max:255|unique:users,email',
                'employee_code'      => 'required|digits:6', // ✅ numeric, exactly 6 digits
                'role'               => 'required|in:EMPLOYEE,USER,MENTOR',
                'department_id'      => 'required|integer|exists:departments,id',
                'status'             => 'required|in:Active,Inactive,Pending',
                'avatar'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // ✅ 5MB
                'send_welcome_email' => 'nullable|boolean',
            ];
        }

        /**
         * Custom validation messages.
         */
        public function messages(): array
        {
            return [
                'role.in'               => 'The selected role is invalid.',
                'department_id.exists'  => 'The selected department is invalid.',
                'email.unique'          => 'This email address is already in use.',
                'avatar.max'            => 'The user avatar must not be greater than 5MB.', // ✅ fixed field name
                'status.in'             => 'The selected status is invalid.',
                'employee_code.required'=> 'Employee code is required.',
                'employee_code.digits'  => 'Employee code must be exactly 6 digits.',
            ];
        }
}
