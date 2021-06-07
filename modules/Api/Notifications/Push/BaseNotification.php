<?php

namespace Modules\Api\Notifications\Push;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Modules\Api\Http\Resources\Notifications\NotificationResource;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

abstract class BaseNotification extends Notification
{
    protected User $user;

    protected string $resourceClass = NotificationResource::class;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(User $user): array
    {
        $drivers = ['database'];

        if ($user->fcm_token) {
            $drivers[] = FcmChannel::class;
        }

        return $drivers;
    }

    public function toDatabase(User $notifiable): array
    {
        return array_merge($this->toArray($notifiable), ['resourceClass' => $this->resourceClass]);
    }

    public function toArray(User $notifiable): array
    {
        return ['user' => $this->user->append('avatar')];
    }

    public function toFcm(User $user): FcmMessage
    {
        return FcmMessage::create()->setNotification(
            FcmNotification::create()
                ->setTitle(config('app.name'))
                ->setBody($this->generateMessage())
                ->setImage($this->user->avatar)
        );
    }

    protected function generateMessage(): string
    {
        return __($this->getTranslationKey(), ['nickname' => $this->user->nickname]);
    }

    protected function getTranslationKey(): string
    {
        $type = (new \ReflectionClass($this))->getShortName();
        return 'tattoo.notifications.' . NotificationResource::$typesMessages[$type];
    }
}
