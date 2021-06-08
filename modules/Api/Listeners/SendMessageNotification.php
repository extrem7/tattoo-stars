<?php

namespace Modules\Api\Listeners;

use Modules\Api\Events\NewMessage;
use Modules\Api\Notifications\NewMessageNotification;

class SendMessageNotification
{
    public function handle(NewMessage $event): void
    {
        \Notification::send($event->participant, new NewMessageNotification($event->message));
    }
}
