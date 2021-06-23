<?php

namespace Modules\Api\Http\Resources;

use App\Models\Chat\Chat;
use App\Models\User;
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
            /* @var $participant User */
            $participant = $chat->participants->where('id', '!=', \Auth::id())->first();
            $owner = $chat->participants->where('id', '!=', $chat->user_id)->first();
        }

        if (!$this->listMessages) {
            $lastMessage = $chat->lastMessage;
        }

        $id = \Auth::id();

        return [
            'id' => $chat->id,
            'user' => $this->when($chat->relationLoaded('participants'), fn() => [
                'id' => $participant->id,
                'name' => $participant->name,
                'avatar' => $participant->getAvatar('icon'),
                'inBlacklist' => $participant->blockers()->where('id', '=', $id)->exists(),
                'inBlockers' => $participant->blacklist()->where('id', '=', $id)->exists(),
            ]),
            $this->mergeWhen($this->listMessages, fn() => [
                'messages' => MessageResource::collection($chat->messages),
                'hasMorePages' => $chat->messages->hasMorePages()
            ]),
            'lastMessage' => $this->when(!$this->listMessages && $lastMessage, fn() => new MessageResource($lastMessage)),
            'unreadCount' => $this->when(isset($this->unreadCount), $this->unreadCount),
            'marked' => $chat->relationLoaded('participants') && $owner->pivot->marked
        ];
    }
}
