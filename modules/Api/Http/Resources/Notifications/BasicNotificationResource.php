<?php

namespace Modules\Api\Http\Resources\Notifications;

class BasicNotificationResource extends NotificationResource
{
    protected function generateMessage(): string
    {
        return __($this->getTranslationKey(), ['nickname' => $this->user['nickname']]);
    }
}
