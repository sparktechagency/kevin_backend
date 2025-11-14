<?php

namespace App\Service\Coach;

use App\Models\Coach;
use App\Traits\ResponseHelper;

class IndexService
{
   use ResponseHelper;

   public function index($chat_id,$request)
    {
        $user = auth()->user();

        $messages = Coach::where('user_id', $user->id)
                        ->where('chat_id', $chat_id)
                        ->orderBy('created_at', 'asc')
                        ->paginate($request->per_page ?? 20);

        return $this->successResponse([
            'success' => true,
            'data' => $messages
        ]);
    }

}
