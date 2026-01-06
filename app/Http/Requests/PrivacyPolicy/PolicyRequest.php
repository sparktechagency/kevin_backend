<?php

namespace App\Http\Requests\PrivacyPolicy;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'nullable|string', // <-- now optional
        ];
    }

    public function messages(): array
    {
        return [
            'content.string' => 'The privacy policy must be a valid text.',
        ];
    }
}
