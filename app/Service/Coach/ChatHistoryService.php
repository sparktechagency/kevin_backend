<?php

namespace App\Service\Coach;

use App\Models\Chat;
use App\Traits\ResponseHelper;

class ChatHistoryService
{
    use ResponseHelper;
    public function history()
    {
        $chats = Chat::orderBy('created_at', 'desc')->get();
        return $this->successResponse($chats, "Chat history retrieved successfully.");
    }

}
