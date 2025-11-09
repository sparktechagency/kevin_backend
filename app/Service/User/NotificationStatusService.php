<?php

namespace App\Service\User;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class NotificationStatusService
{
   use ResponseHelper;
  public function notificationStatus($request)
    {
        $user = Auth::user();

        // Toggle notification status
        $user->is_notification = !$user->is_notification;
        $user->save();

        $status = $user->is_notification ? 'enabled' : 'disabled';

        return $this->successResponse([
            'is_notification' => $user->is_notification
        ], "Notifications have been {$status} successfully.");
    }
}
