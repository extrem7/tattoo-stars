<?php

/**
 * @apiDefine UserProfile
 * @apiSuccess {Object} user Retrieved user.
 * @apiSuccess {Number} user.id User id.
 * @apiSuccess {String} user.avatar User avatar.
 * @apiSuccess {String} user.name User name.
 * @apiSuccess {String} user.location User location.
 * @apiSuccess {String} user.bio User bio.
 * @apiSuccess {Boolean} user.inSubscriptions User in current user's subscriptions.
 * @apiSuccess {Number} user.postsCount User's posts count.
 * @apiSuccess {Number} user.subscribersCount User's subscribers count.
 * @apiSuccess {Number} user.subscriptionsCount User's subscriptions count.
 * @apiSuccess {String} user.phone User phone.
 * @apiSuccess {String} user.website User website.
 * @apiSuccess {String} user.instagram User instagram.
 * @apiSuccess {String} user.facebook User facebook.
 *
 * @apiSuccess {Object[]} posts User's posts.
 * @apiSuccess {Number} posts.id Post id.
 * @apiSuccess {String} posts.description Post description.
 * @apiSuccess {String[]} posts.images Post images.
 * @apiSuccess {Object} posts.video Post video.
 * @apiSuccess {String} posts.video.thumbnail Video thumbnail.
 * @apiSuccess {String} posts.video.url Video url.
 */

/**
 * @apiDefine Pagination
 * @apiParam {Number} [page] Page number for pagination.
 * @apiSuccess {Boolean} hasMorePages Indicates whether more posts can be loaded.
 */

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Users\IndexRequest;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Http\Resources\SubscriberResource;
use Modules\Api\Http\Resources\UserProfileResource;

class UserController extends Controller
{
    protected int $limit = 18;

    /**
     * @api {get} /users Users index.
     * @apiName UserIndex
     * @apiGroup Users
     *
     * @apiParam {Number} account_type_id Users account type.
     * @apiParam {String} [country_id] Users country.
     * @apiParam {String} [city_id] Users city.
     * @apiParam {Integer} [style_id] Users style.
     *
     * @apiUse Token
     *
     * @apiSuccess {Object[]} users User.
     * @apiSuccess {Integer} users.id User id.
     * @apiSuccess {String} users.icon User icon.
     * @apiSuccess {String} users.name User name.
     * @apiSuccess {String} users.nickname User nickname.
     * @apiSuccess {String} users.location User location.
     *
     * @apiUse Pagination
     */
    public function index(IndexRequest $request)
    {
        $user = \Auth::user();
        $validated = $request->validated();

        $users = User::where('account_type_id', '=', $validated['account_type_id'])
            ->whereNotIn('id', $user->blacklist()->pluck('id'))
            ->select(['id', 'name', 'nickname'])
            ->with([
                'avatarMedia',
                'information' => fn($q) => $q->select(['user_id', 'city_id']), 'information.city'
            ])
            ->whereHas('information', function ($q) use ($validated) {
                $q->when(isset($validated['country_id']),
                    fn($q) => $q->whereHas('city',
                        fn($q) => $q->where('country_id', '=', $validated['country_id']))
                )->when(isset($validated['city_id']), fn($q) => $q->where('city_id', '=', $validated['city_id']));
            })
            ->when(
                isset($validated['style_id']),
                fn($q) => $q->whereHas('styles', fn($q) => $q->whereIn('id', [$validated['style_id']]))
            )
            ->simplePaginate($this->limit, []);

        return response()->json([
            'users' => SubscriberResource::collection($users),
            'hasMorePages' => $users->hasMorePages()
        ]);
    }

    /**
     * @api {get} /users/self Get current user information and latest posts
     * @apiName SelfUser
     * @apiGroup Users
     *
     * @apiUse Token
     * @apiUse UserProfile
     *
     * @apiSuccess {Boolean} user.inSubscriptions .
     */
    public function self(): JsonResponse
    {
        return $this->show(\Auth::user());
    }

    /**
     * @api {get} /users/:id Get user information and latest posts
     * @apiName ShowUser
     * @apiGroup Users
     *
     * @apiUse Token
     * @apiUse UserProfile
     */
    public function show(User $user): JsonResponse
    {
        $posts = $user->posts()
            ->select(['id', 'description', 'created_at'])
            ->with(['imagesMedia', 'videoMedia'])
            ->latest()
            ->simplePaginate(12);

        return response()->json([
            'user' => new UserProfileResource($user),
            'posts' => PostResource::collection($posts),
            'hasMorePages' => $posts->hasMorePages()
        ]);
    }
}
