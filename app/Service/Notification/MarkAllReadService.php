<?php

namespace App\Service\Notification;

use App\Traits\ResponseHelper;

class MarkAllReadService
{
    use ResponseHelper;
    public function markAllAsRead()
    {
        $user = auth()->user();
        if ($user->unreadNotifications->isEmpty()) {
            return $this->successResponse(null, 'No unread notifications found.');
        }
        $user->unreadNotifications->markAsRead();
        return $this->successResponse(null, 'All notifications marked as read.');
    }
}
