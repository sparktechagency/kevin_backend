<?php

namespace App\Service\Coach;

use App\Models\Chat;
use App\Models\Coach;
use App\Traits\ResponseHelper;

class ViewService
{
   use ResponseHelper;

   public function view($chat_id, $coach_id)
    {
         $chat = Chat::find($chat_id);
        if (!$chat) {
            return $this->errorResponse('Chat not found');
        }
        $coach = Coach::find($coach_id);
        if (!$coach) {
            return $this->errorResponse('Message not found');
        }
        $user = auth()->user();
        $message = Coach::where('chat_id', $chat_id)
                        ->where('id', $coach_id)
                        ->where('user_id', $user->id)
                        ->first();
        return $this->successResponse([
            'success' => true,
            'data' => $message->message
        ]);
    }
}
