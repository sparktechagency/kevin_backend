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
        if ($user->is_notification == true) {
            // $this->sendPushToAllUsers($notify);
            $this->sendNotificationToRole($notify->role, $notify);
        } else {
            $this->sendNotificationToRole($notify->role, $notify);
        }

        return $this->successResponse($notify,'Notification sent successfully.');
    }

    private function sendPushToAllUsers($notify)
    {
        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            $user->notify(new FcmNotify($notify));
        }
    }

    private function sendNotificationToRole($role, $notify)
    {
        $users = User::where('role', $role)
                     ->get();

        foreach ($users as $user) {
            $user->notify(new FcmNotify($notify));
        }
    }
}
