<?php

namespace App\Service\Notification;

use App\Models\User;
use App\Notifications\AppNotification;

class NotificationService
{
  public function send($users, array $data)
    {
        if ($users instanceof User) {
            $users->notify(new AppNotification($data));
        } else {
            foreach ($users as $user) {
                $user->notify(new AppNotification($data));
            }
        }
        return true;
    }
}
