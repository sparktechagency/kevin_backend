<?php

namespace App\Http\Requests\VoiceNote;

use Illuminate\Foundation\Http\FormRequest;

class VoiceNoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'voice' => 'required', // 50MB limit
            // 'voice' => 'nullable|file|mimes:mp3,m4a|mimetypes:audio/mpeg,audio/mp4,audio/mp4a-latm,audio/aac|max:51200', // 50MB limit
        ];
    }

    /**
     * Custom messages for validation errors
     */
    // public function messages(): array
    // {
    //     return [
    //         'title.required' => 'Please provide a title for the voice note.',
    //         'title.string' => 'The title must be a valid text.',
    //         'title.max' => 'The title cannot exceed 255 characters.',
    //         'description.string' => 'The description must be a valid text.',
    //         'voice.file' => 'The voice must be a valid file.',
    //         'voice.mimes' => 'Only mp3,m4a files are allowed.',
    //         'voice.max' => 'The voice file cannot be larger than 50MB.',
    //     ];
    // }
}
