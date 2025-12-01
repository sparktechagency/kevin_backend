<?php

namespace App\Http\Requests\GoalGererate;

use Illuminate\Foundation\Http\FormRequest;

class GoalReqeust extends FormRequest
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
            'goal_name'   => 'required|string|max:255',
            'employee_id' => 'required|exists:users,id',
            'mentor_id'   => 'required|exists:users,id',
            'status'      => 'nullable|boolean',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'goal_name.required'   => 'The goal name is required.',
            'goal_name.string'     => 'The goal name must be a string.',
            'goal_name.max'        => 'The goal name may not be greater than 255 characters.',
            'employee_id.required' => 'Please select an employee for this goal.',
            'employee_id.exists'   => 'The selected employee does not exist.',
            'mentor_id.required'   => 'Please select a mentor for this goal.',
            'mentor_id.exists'     => 'The selected mentor does not exist.',
            'status.boolean'       => 'The status must be true or false.',
        ];
    }
}
