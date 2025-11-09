<?php

namespace App\Service\VoiceNote;

use App\Models\VoiceNote;
use App\Traits\ResponseHelper;

class IndexService
{
    use ResponseHelper;
   public function index($request)
    {
        $voiceNotes = VoiceNote::with(['user'])->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);

        return $this->successResponse($voiceNotes, 'Voice notes retrieved successfully');
    }
}
