<?php

namespace App\Service\VoiceNote;

use App\Models\VoiceNote;
use App\Traits\ResponseHelper;

class ViewService
{
   use ResponseHelper;
   public function view($voice_id)
    {
        $voiceNote = VoiceNote::with(['user'])->where('id', $voice_id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$voiceNote) {
            return $this->errorResponse('Voice note not found', [], 404);
        }
        return $this->successResponse($voiceNote, 'Voice note retrieved successfully');
    }
}
