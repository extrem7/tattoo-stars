<?php

namespace Modules\Api\Services;

use App\Models\ContestWork;
use App\Models\Story;

class ContestService
{
    public function startDailyContest(): void
    {
        Story::whereDate('created_at', '=', today()->addDays(-1))
            ->groupBy('post_id')
            ->whereNull('excluded')
            ->orderByDesc('total')
            ->limit(10)
            ->get(['post_id', \DB::raw('sum(rating) as total')])
            ->each(fn(Story $item) => ContestWork::create(['post_id' => $item->post_id, 'rating' => $item->total]));
    }

    public function calculateResults(): void
    {
        $works = ContestWork::yesterday()->withCount('votes')->orderByDesc('votes_count')->get('id');

        /* @var $winner ContestWork */
        if ($winner = $works->first()) {
            $winner->winner = true;
            $winner->save();
        }
    }
}
