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
            'userId' => $message->user_id,
            'text' => $message->text,
            'createdAt' => $message->created_at,
            'viewed' => $message->viewed
        ];
    }
}
