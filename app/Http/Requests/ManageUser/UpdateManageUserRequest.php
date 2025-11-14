<?php

namespace App\Http\Requests\ManageUser;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManageUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

  public function rules(): array
    {
        $userId = $this->route('id'); // route should be like /users/{id}

        return [
            'name'          => 'required|string|min:2|max:255',
            'email'         => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'employee_code' => 'required|digits:6',
            'role'          => 'required|in:EMPLOYEE,USER,MENTOR',
            'department_id' => 'required|integer|exists:departments,id',
            'status'        => 'required|in:Active,Inactive,Pending',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'send_welcome_email' => 'nullable|boolean',
        ];
    }


    public function messages(): array
    {
        return [
            'email.unique'          => 'This email address is already in use.',
            'employee_code.required'=> 'Employee code is required.',
            'employee_code.digits'  => 'Employee code must be exactly 6 digits.',
            'role.in'               => 'The selected role is invalid.',
            'department_id.exists'  => 'The selected department is invalid.',
            'status.in'             => 'The selected status is invalid.',
            'avatar.max'            => 'The user avatar must not be greater than 5MB.',
        ];
    }
}
