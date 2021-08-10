<?php

namespace Modules\Api\Repositories;

use App\Models\Advertising\Banner;
use App\Models\ContestWork;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class ContestRepository
{
    /* @return Collection<ContestWork>|ContestWork[] */
    public function getDaily(): Collection
    {
        return ContestWork::daily()
            ->with([
                'post' => fn(BelongsTo $q) => $q->with(['user', 'imagesMedia', 'videoMedia']),
                'votes' => fn(BelongsToMany $q) => $q->where('user_id', '=', \Auth::id())
            ])
            ->orderByDesc('rating')
            ->get(['id', 'post_id']);
    }

    /* @return Collection<ContestWork>|ContestWork[] */
    public function getYesterdayWinners(): Collection
    {
        return ContestWork::yesterday()
            ->with(['post' => fn(BelongsTo $q) => $q->with(['user', 'imagesMedia', 'videoMedia'])])
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get(['id', 'post_id']);
    }

    public function getYesterdayVotesCount(): int
    {
        return \DB::table('contest_votes')
            ->whereRaw('exists (select * from contest where date = subdate(current_date, 1))')
            ->count();
    }

    /* @return Collection<ContestWork>|ContestWork[] */
    public function getWinners(): Collection
    {
        return ContestWork::whereNotNull('winner')
            ->with(['post' => fn(BelongsTo $q) => $q->with(['user', 'imagesMedia', 'videoMedia'])])
            ->orderByDesc('date')
            ->get(['id', 'post_id', 'date']);
    }

    public function getBanner(): ?Banner
    {
        $city = \Auth::user()->information->city;

        return Banner::active()
            ->inRandomOrder()
            ->where(fn(Builder $q) => $q
                ->where(fn(Builder $q) => $q->whereNull('country_id')->whereNull('city_id'))
                ->orWhere(
                    fn(Builder $q) => $q->when(
                        $city, fn(Builder $q) => $q
                        ->where('country_id', '=', $city->country_id)
                        ->where(
                            fn(Builder $q) => $q
                                ->whereNull('city_id')
                                ->orWhere('city_id', '=', $city->id)
                        )
                    )
                )
            )->first(['id', 'user_id', 'redirect_to_site', 'site_url']);
    }
}
