<?php

namespace Modules\Api\Repositories;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Modules\Api\Services\PostService;

class ChatRepository
{
    /* @return Collection<Chat> */
    public function getForIndex(): Collection
    {
        $user = \Auth::user();

        return $user->chats()
            ->with([
                'participants' => fn(BelongsToMany $q) => $q->withPivot('marked')->with('avatarMedia'),
                'lastMessage.imageMedia'
            ])
            ->withCount('messages')
            ->get()
            ->filter(fn(Chat $c) => $c->messages_count || $c->user_id === $user->id)
            ->sort(
                fn(Chat $a, Chat $b) => in_array(null, [$a->lastMessage, $b->lastMessage], true)
                    ? $a->created_at < $b->created_at
                    : $a->lastMessage->created_at < $b->lastMessage->created_at
            );
    }

    public function getForShow(Chat $chat): Chat
    {
        $chat->load(['participants' => fn(BelongsToMany $q) => $q->withPivot('marked')->with('avatarMedia')]);

        $messages = $chat->messages()->with('imageMedia')->latest()->simplePaginate(50);
        $chat->setRelation('messages', $messages);

        return $chat;
    }

    public function findOrStartChat(User $user): Chat
    {
        $authUser = \Auth::user();
        $chat = $authUser->chats()->whereHas(
            'participants', fn(Builder $q) => $q->where('id', '=', $user->id)
        )->first();

        if ($chat === null) {
            /* @var $chat Chat */
            $chat = $authUser->chats()->create();
            $chat->participants()->sync([$authUser->id, $user->id]);
        }

        return $chat;
    }

    public function createMessage(Chat $chat, string $text = null, UploadedFile $image = null): Message
    {
        return \DB::transaction(function () use ($chat, $text, $image): Message {
            /* @var $message Message */
            $message = $chat->messages()->create([
                'user_id' => \Auth::id(),
                'text' => $text
            ]);

            if ($image) {
                $service = app(PostService::class);
                $service->compressImage($image);
                $media = $message->addMedia($image)->toMediaCollection('image');
                $message->setRelation('imageMedia', $media);
            }

            return $message;
        });
    }
}
