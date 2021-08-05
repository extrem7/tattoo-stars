<?php

namespace Modules\Api\Http\Resources\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;

class AdvertisingNotification extends NotificationResource
{
    protected array $top;

    public function __construct(DatabaseNotification $resource)
    {
        $this->resource = $resource;
        $this->type = 'AdvertisingNotification';

        $this->top = $resource->data['top'];
    }

    public function toArray($request = null): array
    {
        return [
            'type' => $this->type,
            'text' => $this->generateMessage(),
            'end_at' => $this->top['end_at']
        ];
    }

    protected function generateMessage(): string
    {
        return __($this->getTranslationKey(), ['date' => Carbon::parse($this->top['end_at'])->format('d.m.Y')]);
    }
}
