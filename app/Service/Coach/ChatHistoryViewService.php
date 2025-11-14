<?php

namespace App\Service\Coach;

use App\Models\Coach;
use App\Traits\ResponseHelper;

class ChatHistoryViewService
{
   use ResponseHelper;
  public function chatHistoryView($chat_id)
    {
        $userId = auth()->id();

        $messages = Coach::where('chat_id', $chat_id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'Chat messages fetched successfully.',
            'data' => $messages
        ]);
    }

}
