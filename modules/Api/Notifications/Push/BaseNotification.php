<?php

namespace Modules\Api\Notifications\Push;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Modules\Api\Http\Resources\Notifications\BasicNotificationResource;

abstract class BaseNotification extends Notification
{
    protected User $user;

    protected string $resourceClass = BasicNotificationResource::class;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        return array_merge($this->toArray($notifiable), ['resourceClass' => $this->resourceClass]);
    }

    public function toArray(User $notifiable): array
    {
        return ['user' => $this->user->append('avatar')];
    }
}
