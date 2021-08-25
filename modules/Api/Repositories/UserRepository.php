<?php

namespace Modules\Api\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserRepository
{
    protected int $limit = 18;

    public function getUsers(array $params): Paginator
    {
        return User::where('account_type_id', '=', $params['account_type_id'])
            ->whereNotIn('id', $params['blacklist'])
            ->whereDoesntHave('tops', fn(Builder $q) => $q->active())
            ->select(['id', 'name', 'nickname'])
            ->with([
                'avatarMedia',
                'information' => fn($q) => $q->select(['user_id', 'city_id']),
                'information.city', 'information.city.country',
                'contestWorks' => fn(HasManyThrough $q) => $q->whereNotNull('winner')
            ])
            ->withCount(['subscribers'])
            ->whereHas('information', function ($q) use ($params) {
                $q->when(isset($params['country_id']),
                    fn($q) => $q->whereHas('city',
                        fn($q) => $q->where('country_id', '=', $params['country_id']))
                )->when(isset($params['city_id']), fn($q) => $q->where('city_id', '=', $params['city_id']));
            })
            ->when(
                isset($params['style_id']),
                fn($q) => $q->whereHas('styles', fn($q) => $q->whereIn('id', [$params['style_id']]))
            )
            ->orderByDesc('subscribers_count')
            ->simplePaginate($this->limit, []);
    }

    public function getTopUsers(array $params): Collection
    {
        return User::activeTop()
            ->where('account_type_id', '=', $params['account_type_id'])
            ->whereNotIn('id', $params['blacklist'])
            ->select(['id', 'name', 'nickname'])
            ->with([
                'avatarMedia',
                'information' => fn($q) => $q->select(['user_id', 'city_id']),
                'information.city', 'information.city.country',
                'styles',
                'posts' => fn($q) => $q->with(['imagesMedia', 'videoMedia'])->limit(5)->latest(),
                'contestWorks' => fn($q) => $q->whereNotNull('winner')
            ])
            ->withCount(['subscribers'])
            ->whereHas('information', function ($q) use ($params) {
                $q->when(isset($params['country_id']),
                    fn($q) => $q->whereHas('city',
                        fn($q) => $q->where('country_id', '=', $params['country_id']))
                )->when(isset($params['city_id']), fn($q) => $q->where('city_id', '=', $params['city_id']));
            })
            ->when(
                isset($params['style_id']),
                fn($q) => $q->whereHas('styles', fn($q) => $q->whereIn('id', [$params['style_id']]))
            )
            ->limit(5)
            ->inRandomOrder()
            ->get()
            ->map(fn(User $user) => $user->setRelation(
                'posts', $user->posts()->with(['imagesMedia', 'videoMedia'])->limit(5)->latest()->get())
            );
    }

    public function getBlacklist(User $user): Collection
    {
        return $user->blacklist()->with(['avatarMedia', 'information.city'])->get(['id', 'name', 'nickname']);
    }

    public function getSubscriptions(User $user, string $scope, string $query = null): Paginator
    {
        if (!method_exists(User::class, $scope)) {
            throw new \InvalidArgumentException;
        }

        /* @var $items Builder<User> */
        $items = $user->$scope();
        $items = $items->select(['id', 'name', 'nickname'])
            ->with([
                'avatarMedia',
                'information' => fn(Relation $q) => $q->select('user_id', 'city_id'), 'information.city'
            ])
            ->when($query, fn($q) => $q->where(
                fn($q) => $q->where('nickname', 'like', "%$query%")->orWhere('name', 'like', "%$query%")
            ))
            ->simplePaginate($this->limit, []);

        return $items;
    }
}
