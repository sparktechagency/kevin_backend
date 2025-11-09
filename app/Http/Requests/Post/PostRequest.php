<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'content' => 'nullable|string|max:2000',  // Limit content to a maximum of 2000 characters
            'photos' => 'nullable|array',  // Ensure photos is an array
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,bmp|max:102400', // Each photo must be a valid file (up to 100MB)
            'privacy' => 'in:public,connections,private',  // Valid privacy values
        ];
    }

    public function messages()
    {
        return [
            'content.max' => 'The content may not be greater than 2000 characters.',
            'photos.*.file' => 'Each photo must be a valid file.',  // Corrected to validate file type
            'photos.*.mimes' => 'Each photo must be of type: jpg, jpeg, png, gif, bmp.', // Custom message for valid photo file types
            'photos.*.max' => 'Each photo size may not be greater than 100 MB.', // Custom message for max file size
            'privacy.in' => 'The privacy must be one of the following values: public, connections, private.',  // Privacy validation message
        ];
    }
}
