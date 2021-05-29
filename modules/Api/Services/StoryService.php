<?php

namespace Modules\Api\Services;

use App\Models\Story;

class StoryService
{
    public function toggleDislike(Story $story): bool
    {
        return $this->toggleLike($story, true);
    }

    public function toggleLike(Story $story, bool $dislike = null): bool
    {
        $user = \Auth::user();

        $mark = $user->marks()->withPivot('is_dislike')->find($story->id);
        if ($mark && (bool)$mark->pivot->is_dislike !== (bool)$dislike) {
            $user->marks()->updateExistingPivot($story->id, ['is_dislike' => $dislike]);
            $changed = true;
        } else {
            $changes = $user->marks()->toggle([$story->id => ['is_dislike' => $dislike]]);
            $changed = !empty($changes['attached']);
        }

        $story->update(['rating' => $story->likes()->count() - $story->dislikes()->count()]);

        return $changed;
    }

    public function view(Story $story): bool
    {
        return \Auth::user()->views()->save($story) !== null;
    }
}
