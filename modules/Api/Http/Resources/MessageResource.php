<?php

namespace Modules\Api\Http\Resources;

use App\Models\Chat\Message;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request = null): array
    {
        /* @var $message Message */
        $message = $this->resource;

        return [
            'id' => $message->id,
            'chatId' => $message->chat_id,
            'userId' => $message->user_id,
            'text' => $message->text,
            'image' => $this->when(
                $message->relationLoaded('imageMedia') && $message->imageMedia, fn() => $message->imageMedia->getFullUrl()
            ),
            'video' => $this->when($message->videoMedia, fn() => [
                'thumbnail' => $message->videoMedia->getFullUrl('thumbnail'),
                'url' => $message->videoMedia->getFullUrl()
            ]),
            'createdAt' => $message->created_at,
            'viewed' => $message->viewed
        ];
    }
}
