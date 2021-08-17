<?php

namespace Modules\Api\Services;

use App\Models\ContestWork;
use App\Models\Story;

class ContestService
{
    public function startDailyContest(): void
    {
        $stories = Story::yesterday()
            ->groupBy('post_id')
            ->whereNull('excluded')
            ->orderByDesc('total')
            ->limit(10)
            ->get(['post_id', \DB::raw('sum(rating) as total')]);

        $included = Story::yesterday()
            ->whereNotNull('included')
            ->groupBy('post_id')
            ->orderByDesc('total')
            ->get(['post_id', \DB::raw('sum(rating) as total')]);

        if (($overflow = $stories->count() - 10 + $included->count()) && $overflow > 0) {
            $stories = $stories->slice(0, -$overflow);
        }
        $stories = $stories->concat($included);

        \Log::info('Stories for : ' . today()->addDays(-1) . '\n' . print_r($stories->toArray(), true));

        $stories->each(fn(Story $item) => ContestWork::create(['post_id' => $item->post_id, 'rating' => $item->total]));
    }

    public function calculateResults(): void
    {
        $works = ContestWork::yesterday()->withCount('votes')->orderByDesc('votes_count')->get('id');

        \Log::info('Works for : ' . today()->addDays(-1) . print_r($works->toArray(), true));

        /* @var $winner ContestWork */
        if ($winner = $works->first()) {
            \Log::info('Mark winner work: ' . print_r($winner->toArray(), true));
            $winner->winner = true;
            $winner->save();
        }
    }
}
