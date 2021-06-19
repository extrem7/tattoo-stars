<?php

namespace Modules\Api\Notifications;

use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via(User $user): array
    {
        $drivers = [];

        if ($user->fcm_token) {
            $drivers[] = FcmChannel::class;
        }

        return $drivers;
    }

    public function toFcm(User $user): FcmMessage
    {
        $author = $this->message->user;

        return FcmMessage::create()->setNotification(
            FcmNotification::create()
                ->setTitle(config('app.name'))
                ->setBody(__('tattoo.notifications.new_message', ['nickname' => $author->nickname]))
        );
    }

    public function toArray(User $user): array
    {
        return $this->message->toArray();
    }
}
