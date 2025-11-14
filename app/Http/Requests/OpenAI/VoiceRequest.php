<?php

namespace App\Http\Requests\OpenAI;

use Illuminate\Foundation\Http\FormRequest;

class VoiceRequest extends FormRequest
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
            'voice' => 'required|file|mimes:mp3,wav,m4a',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'audio.required' => 'Please upload an audio file.',
            'audio.file' => 'The uploaded file must be a valid file.',
            'audio.mimes' => 'Only audio files with extensions mp3, wav, or m4a are allowed.',
        ];
    }
}
