<?php

namespace App\Http\Requests\Category;

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
            // The category name must be unique and a string with a max length of 255 characters.
            'name' => 'required|string|max:255|unique:categories,name',

            // The icon is optional but must be an image file (png, jpg, jpeg), with a max size of 50MB.
            'icon' => 'nullable|image|mimes:png,jpg,jpeg|max:51200',  // 50MB is 51200KB
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Custom error messages for the 'name' field
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a valid string.',
            'name.max' => 'The category name cannot be longer than 255 characters.',
            'name.unique' => 'The category name must be unique.',
            'icon.image' => 'The icon must be an image file.',
            'icon.mimes' => 'The icon must be a file of type: png, jpg, jpeg.',
            'icon.max' => 'The icon cannot exceed 50MB.',
        ];
    }
}
