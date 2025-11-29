<?php

namespace App\Service\Notification;

use App\Models\Notify;
use App\Models\User;
use App\Notifications\FcmNotify;
use App\Traits\ResponseHelper;

class CreateService
{
    use ResponseHelper;

    public function create($data)
    {
        $user = auth()->user();

        // 1. Save Notification
        $notify = Notify::create([
            'name'    => $data['name'],
            'message' => $data['message'],
            'role'    => $data['role'],
            'status'  => $data['status'],
        ]);

        // 2. Draft → only save
        if ($notify->status === 'Draft') {
            return $this->successResponse($notify, 'Notification saved as Draft.');
        }

        // 3. Send Notification
        $this->sendNotificationToRole($notify->role, $notify, $user);

        return $this->successResponse($notify,'Notification sent successfully.');
    }

    private function sendNotificationToRole($role, $notify, $sender)
    {
        $users = User::where('role', $role)->get();

        $notificationData = [
            'name'    => $notify->name,
            'message' => $notify->message,
            'type'    => $sender->role ?? 'USER', // corrected variable
        ];

        $notificationService = new NotificationService();
        $notificationService->send($users, $notificationData);
    }
}
