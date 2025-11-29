<?php

namespace App\Service\VoiceNote;

use App\Models\VoiceNote;
use App\Traits\ResponseHelper;
use OpenAI\Laravel\Facades\OpenAI;

class StoreService
{
    use ResponseHelper;

    public function store($data)
    {
        $userId = auth()->id();
        $voicePath = null;
        $transcript = null;

        if (!empty($data['voice'])) {
            // Store voice file
            $voicePath = $data['voice']->store('voice_notes', 'public');

            // Correct public URL path
            $data['voice'] = 'storage/' . $voicePath;
        }
// return $data;
        // Save Voice Note
        $voiceNote = VoiceNote::create([
            'user_id'     => $userId,
            'voice'       => $voicePath,
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'transcript'  => $transcript,
        ]);

        return $this->successResponse([
            'voice_note' => $voiceNote
        ], 'Voice note created successfully with transcription.');
    }
}
