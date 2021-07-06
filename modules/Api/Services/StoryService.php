<?php

namespace Modules\Api\Services;

use App\Models\Story;
use App\Models\User;

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

    public function makeTranslation(User $user, int $amount, bool $daily = false): bool
    {
        $balance = $this->calculateStoryBalance($user) + $amount;

        if ($balance < 0) {
            abort(400, 'Balance can\'t be negative.');
        }

        $translation = $user->storyTransitions()->create([
            'amount' => $amount,
            'balance' => $balance,
            'daily' => $daily ?: null
        ]);

        $user->story_balance = $balance;
        $user->save();

        return $translation !== null;
    }

    public function dailyBalanceBonus(): void
    {
        User::where('story_balance', '=', 0)->get('id')->each(fn(User $u) => $this->makeTranslation($u, 10, true));
    }

    protected function calculateStoryBalance(User $user): int
    {
        return $user->storyTransitions()->sum('amount');
    }
}
