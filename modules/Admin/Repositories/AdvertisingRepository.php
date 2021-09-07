<?php

namespace Modules\Admin\Repositories;

use App\Models\Advertising\Banner;
use App\Models\Advertising\Promotion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AdvertisingRepository
{
    public function getPromotionsForIndex(array $search, \Closure $scope = null): LengthAwarePaginator
    {
        return Promotion::with(['post' => fn($q) => $q->with(['user', 'imagesMedia', 'videoMedia']), 'city.country'])
            ->when($search['searchQuery'] ?? false, fn(Builder $q) => $q->search($search['searchQuery']))
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

            $images = $promotion->post->imagesMedia->map(fn(Media $m) => $m->getFullUrl());
            if ($promotion->post->videoMedia) {
                $images[] = $promotion->post->videoMedia->getFullUrl('thumb');
            }
            $data['images'] = $images;

            $data['user'] = $promotion->post->user;
            return $data;
        });
    }

    public function getBannersForIndex(array $search, \Closure $scope = null): LengthAwarePaginator
    {
        return Banner::with(['imageMedia', 'city.country'])
            ->when($search['searchQuery'] ?? false, fn(Builder $q) => $q->search($search['searchQuery']))
            ->when($search['sortBy'] ?? false, fn(Builder $q) => $q->orderBy($search['sortBy'], $search['sortDesc'] ?? false ? 'desc' : 'asc'))
            ->when($scope, fn($q) => $scope($q))
            ->latest()
            ->paginate(10);
    }

    public function transformBanners(LengthAwarePaginator $promotions): void
    {
        /* @var $promotions Collection<Banner> */
        $promotions->transform(function (Banner $banner) {
            $data = $banner->toArray();
            if ($city = $banner->city) {
                $data['location'] = "$city->name, {$city->country->name}";
            }
            $data['banner'] = $banner->imageMedia->getFullUrl();
            $data['user'] = $banner->user;
            return $data;
        });
    }
}
