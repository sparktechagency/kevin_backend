<?php

namespace App\Service\Notification;

use App\Models\Notify;
use App\Models\User;
use App\Notifications\FcmNotify;
use App\Traits\ResponseHelper;

class UpdateService
{
    use ResponseHelper;

    public function update($data, $id)
    {
        $user = auth()->user();

        // 1. Find the existing notification
        $notify = Notify::find($id);

        if (!$notify) {
            return $this->errorResponse('Notification not found.');
        }

        // 2. Update the notification
        $notify->update([
            'name'    => $data['name'],
            'message' => $data['message'],
            'role'    => $data['role'],
            'status'  => $data['status'],
        ]);

        // 3. Draft → only save
        if ($notify->status === 'Draft') {
            return $this->successResponse($notify, 'Notification updated as Draft.');
        }

       if ($user->is_notification == true) {
            // $this->sendPushToAllUsers($notify);
            $this->sendNotificationToRole($notify->role, $notify);
        } else {
            $this->sendNotificationToRole($notify->role, $notify);
        }

        return $this->successResponse($notify, 'Notification updated and sent successfully.');
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
                    //  ->whereNotNull('fcm_token')
                     ->get();

        foreach ($users as $user) {
            $user->notify(new FcmNotify($notify));
        }
    }
}
