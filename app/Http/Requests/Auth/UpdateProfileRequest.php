<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'avatar' =>'nullable|image|mimes:png,jpg,jpeg|max:52428800'
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'phone_number.string' => 'The phone number must be a valid string.',
            'phone_number.max' => 'The phone number must not exceed 20 characters.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be of type png, jpg, or jpeg.',
            'image.max' => 'The image size must not exceed 50MB.',
        ];
    }
}
