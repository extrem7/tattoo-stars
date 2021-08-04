<?php

namespace Modules\Admin\Repositories;

use App\Models\Advertising\Promotion;
use App\Models\Traits\Searchable;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PromotionRepository
{
    public function getPromotionsForIndex(array $search, \Closure $scope = null): LengthAwarePaginator
    {
        return Promotion::with(['post.user', 'city.country'])
            ->when($search['searchQuery'] ?? false, fn(Searchable $q) => $q->search($search['searchQuery']))
            ->when($search['sortBy'] ?? false, fn(Builder $q) => $q->orderBy($search['sortBy'], $search['sortDesc'] ?? false ? 'desc' : 'asc'))
            ->when($scope, fn($q) => $scope($q))
            ->latest()
            ->paginate(10);
    }

    public function transformPromotions(LengthAwarePaginator $promotions): void
    {
        /* @var $promotions Collection<User> */
        $promotions->transform(function (Promotion $promotion) {
            $data = $promotion->toArray();
            $data['account_type'] = Promotion::$accountTypes[$data['account_type']];
            if ($city = $promotion->city) {
                $data['location'] = "$city->name, {$city->country->name}";
            }
            if ($video = $promotion->post->videoMedia) {
                $thumbnail = $video->getFullUrl('thumbnail');
            } else {
                $thumbnail = $promotion->post->imagesMedia()->first()->getFullUrl('thumb');
            }
            $data['thumbnail'] = $thumbnail;
            $data['user'] = $promotion->post->user;
            return $data;
        });
    }
}
