<?php

namespace Modules\Api\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Api\Events\NewMessage;
use Modules\Api\Listeners\SendMessageNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewMessage::class => [
            SendMessageNotification::class
        ],
    ];
}
