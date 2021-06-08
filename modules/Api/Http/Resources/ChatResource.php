<?php

namespace Modules\Api\Http\Resources;

use App\Models\Chat\Chat;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    private bool $listMessages = false;

    public function enableListMessages(): self
    {
        $this->listMessages = true;
        return $this;
    }

    public function toArray($request): array
    {
        /* @var $chat Chat */
        $chat = $this->resource;

        if ($chat->relationLoaded('participants')) {
            $participant = $chat->participants->where('id', '!=', \Auth::id())->first();
            $owner = $chat->participants->where('id', '=', $chat->user_id)->first();
        }

        if (!$this->listMessages) {
            $lastMessage = $chat->lastMessage;
        }

        return [
            'id' => $chat->id,
            'user' => $this->when($chat->relationLoaded('participants'), fn() => [
                'id' => $participant->id,
                'name' => $participant->name,
                'avatar' => $participant->getAvatar('icon')
            ]),
            $this->mergeWhen($this->listMessages, fn() => [
                'messages' => MessageResource::collection($chat->messages),
                'hasMorePages' => $chat->messages->hasMorePages()
            ]),
            'lastMessage' => $this->when(!$this->listMessages && $lastMessage, fn() => new MessageResource($lastMessage)),
            'marked' => $chat->relationLoaded('participants') && $owner->pivot->marked
        ];
    }
}