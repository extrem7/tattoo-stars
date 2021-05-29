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
 * @apiSuccess {Boolean} user.inBlacklist User in current user's blacklist.
 * @apiSuccess {Number} user.postsCount User's posts count.
 * @apiSuccess {Number} user.subscribersCount User's subscribers count.
 * @apiSuccess {Number} user.subscriptionsCount User's subscriptions count.
 * @apiSuccess {String} user.phone User phone.
 * @apiSuccess {String} user.email User email.
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

/**
 * @apiDefine Search
 * @apiParam {String} [query] String for search.
 */

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Users\IndexRequest;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Http\Resources\SubscriberResource;
use Modules\Api\Http\Resources\UserProfileResource;
use Modules\Api\Repositories\PostRepository;
use Modules\Api\Repositories\UserRepository;
use Modules\Api\Services\UserService;

final class UserController extends Controller
{
    protected UserService $service;

    protected UserRepository $repository;
    protected PostRepository $postRepository;

    public function __construct(UserService $service, UserRepository $repository, PostRepository $postRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->postRepository = $postRepository;
    }

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
        $params = $request->validated();
        $params['blacklist'] = \Auth::user()->blacklist()->pluck('id');

        $users = $this->repository->getUsers($params);

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
     * @apiUse Posts
     * @apiUse Pagination
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
     * @apiUse Posts
     * @apiUse Pagination
     */
    public function show(User $user): JsonResponse
    {
        $posts = $this->postRepository->getPostsByUser($user);

        \Auth::user()->load(['subscriptions', 'blacklist']);

        return response()->json([
            'user' => new UserProfileResource($user),
            'posts' => PostResource::collection($posts),
            'hasMorePages' => $posts->hasMorePages()
        ]);
    }
}
