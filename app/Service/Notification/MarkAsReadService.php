<?php

namespace App\Service\Notification;

use App\Traits\ResponseHelper;

class MarkAsReadService
{
    use ResponseHelper;

    public function markAsRead($notificationId)
    {
          $user = auth()->user();

        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (!$notification) {
            return $this->errorResponse('Notification not found.');
        }

        $notification->markAsRead();

        return $this->successResponse($notification, 'Notification marked as read.');
    }
}
