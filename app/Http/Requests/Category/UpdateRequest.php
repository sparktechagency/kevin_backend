<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // You can adjust the authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the category ID from the route parameter
          $id = $this->route('id');  // 'category' is the parameter name in the route

        return [
            // The category name must be unique, excluding the current category being updated
            'name' => 'required|string|max:255|unique:categories,name,' . $id,  // Correct uniqueness check

            // The icon is optional, but if provided, must be an image file and can be a maximum of 50MB
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

            // Custom error messages for the 'icon' field
            'icon.image' => 'The icon must be an image file.',
            'icon.mimes' => 'The icon must be a file of type: png, jpg, jpeg.',
            'icon.max' => 'The icon cannot exceed 50MB.',
        ];
    }
}
