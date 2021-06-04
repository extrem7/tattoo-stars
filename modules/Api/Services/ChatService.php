<?php

namespace Modules\Api\Services;

use App\Models\Chat\Chat;

class ChatService
{
    public function markAsViewed(Chat $chat): void
    {
        $chat->messages()->where('user_id', '!=', \Auth::id())->update(['viewed' => true]);
    }

    public function toggleMark(Chat $chat): bool
    {
        $id = \Auth::id();

        $marked = (bool)$chat->participants()->where('id', '=', $id)->withPivot('marked')->first()->pivot->marked;
        $chat->participants()->updateExistingPivot($id, ['marked' => !$marked]);

        return !$marked;
    }
}
