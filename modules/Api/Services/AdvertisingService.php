<?php

namespace Modules\Api\Services;

use App\Models\Advertising\Top;
use Modules\Api\Notifications\AdvertisingNotification;

class AdvertisingService
{
    public function sendExpirationNotifications(): void
    {
        $tops = Top::active()->whereDate('end_at', '<', now()->addDays(3))->get();
        $tops->each(fn(Top $top) => $top->user->notify(new AdvertisingNotification($top)));
    }
}
