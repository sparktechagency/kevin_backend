<?php

namespace App\Service\VoiceNote;

use App\Models\VoiceNote;
use App\Traits\ResponseHelper;

class StoreService
{
  use ResponseHelper;
  public function store($data)
    {
        $voicePath = null;
        if (isset($data['voice'])) {
            $voicePath = $data['voice']->store('voice_notes', 'public');
            $voicePath = 'storage/' . $voicePath;
        }
        $voiceNote = VoiceNote::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'voice' => $voicePath,
        ]);
        return $this->successResponse($voiceNote, 'Voice note created successfully');
    }
}
