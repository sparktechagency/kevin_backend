<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'name'    => 'required|string|max:255',
            'message' => 'required|string',
            'role'    => 'required|string|max:100',
            'status'  => 'required|in:Draft,Send',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'The name field is required.',
            'name.string'      => 'The name must be a valid string.',
            'name.max'         => 'The name may not be greater than 255 characters.',

            'message.required' => 'Please enter a message.',
            'message.string'   => 'The message must be a valid string.',

            'role.required'    => 'Please select a role.',
            'role.string'      => 'The role must be valid.',
            'role.max'         => 'The role may not be greater than 100 characters.',

            'status.required'  => 'Please select a status.',
            'status.in'        => 'The status must be either Draft or Send.',
        ];
    }
}
