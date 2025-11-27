<?php

namespace App\Service\Notification;

use App\Traits\ResponseHelper;

class GetNotificationService
{
   use ResponseHelper;
    public function getNotification($request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'all'); // 'all' or 'unread'
        $perPage = $request->get('per_page', 10);
        // Select notifications query based on type
        $query = $type === 'unread' ? $user->unreadNotifications() : $user->notifications();
        // Get paginated notifications
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return $this->successResponse($notifications, 'Notifications retrieved successfully.');
    }
}
