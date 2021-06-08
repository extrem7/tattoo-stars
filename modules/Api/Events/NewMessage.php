<?php

namespace Modules\Api\Events;

use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Api\Http\Resources\MessageResource;

class NewMessage implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $participant;

    public function __construct(Message $message, User $participant)
    {
        $this->message = $message;
        $this->participant = $participant;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("messages.{$this->participant->id}");
    }

    public function broadcastWith(): array
    {
        return (new MessageResource($this->message))->toArray();
    }

    public function broadcastAs(): string
    {
        return 'chat.message.created';
    }
}
