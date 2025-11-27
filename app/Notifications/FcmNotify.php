<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmNotify extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notify;

    public function __construct($notify)
    {
        $this->notify = $notify;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'name'    => $this->notify->name,
            'message' => $this->notify->message,
            'role'    => $this->notify->role,
            'status'  => $this->notify->status,
        ];
    }

    public function toFcm($notifiable)
    {
        if (!$notifiable->fcm_token) return null;

        $messaging = app('firebase.messaging');

        $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(FcmNotification::create(
                $this->notify->name,
                $this->notify->message
            ));

        $messaging->send($message);
    }
}
