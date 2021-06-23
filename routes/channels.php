<?php

use App\Models\Chat\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel('chat.{chat}', function (User $user, Chat $chat) {
    return $chat->participants()->where('id', '=', $user->id)->exists();
});
