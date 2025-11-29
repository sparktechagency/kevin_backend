<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return $this->data;
    }

    // public function toFcm($notifiable)
    // {
    //     return FcmMessage::create()
    //         ->setData($this->data)
    //         ->setNotification(
    //             FcmNotification::create()
    //                 ->setTitle($this->data['title'] ?? 'Notification')
    //                 ->setBody($this->data['message'] ?? '')
    //         );
    // }
}
