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
        $title = null;
        $description = null;

        if (!empty($data['voice'])) {
            // Store voice file
            $voicePath = $data['voice']->store('voice_notes', 'public');
            $voicePathFull = storage_path('app/public/' . $voicePath);

            // Transcribe audio with Whisper (latest OpenAI SDK)
            $response = OpenAI::audio()->create([
                'file' => fopen($voicePathFull, 'r'),
                'model' => 'whisper-1',
            ]);

            $transcript = $response['text'] ?? null;

            // Generate enhanced transcription, title & description
            if ($transcript) {
                $enhanced = OpenAI::responses()->create([
                    'model' => 'gpt-5-mini',
                    'input' => "Refine this transcription and generate a concise title and description in the format:
                    Title: <title here>
                    Description: <description here>\n\nTranscription:\n$transcript",
                ]);

                $output = $enhanced->output_text ?? $transcript;

                // Extract title and description
                if (preg_match('/Title:(.*)Description:(.*)/s', $output, $matches)) {
                    $title = trim($matches[1]);
                    $description = trim($matches[2]);
                } else {
                    // fallback if GPT format fails
                    $description = $output;
                    $title = substr($output, 0, 50); // first 50 chars as title
                }
            }

            $voicePath = 'storage/' . $voicePath;
        }

        // Save voice note
        $voiceNote = VoiceNote::create([
            'user_id' => $userId,
            'title' => $title ?? $data['title'] ?? null,
            'description' => $description ?? $data['description'] ?? null,
            'voice' => $voicePath,
            'transcript' => $transcript,
        ]);

        return $this->successResponse([
            'voice_note' => $voiceNote
        ], 'Voice note created successfully with transcription, title, and description.');
    }
}
