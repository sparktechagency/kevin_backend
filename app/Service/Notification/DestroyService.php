<?php

namespace App\Service\Notification;

use App\Models\Notify;
use App\Traits\ResponseHelper;

class DestroyService
{
    use ResponseHelper;

    public function destroy($id)
    {
        $notify = Notify::find($id);

        if (!$notify) {
            return $this->errorResponse('Notification not found.');
        }

        $notify->delete();

        return $this->successResponse([], 'Notification deleted successfully.');
    }
}
