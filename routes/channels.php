<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{id}', function (User $user, int $id) {
    return $user->id === $id;
});
