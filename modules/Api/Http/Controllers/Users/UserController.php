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
 *
 * @apiSuccess {Boolean} hasMorePages Indicates whether more posts can be loaded.
 */

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Http\Resources\UserProfileResource;

class UserController extends Controller
{
    /**
     * @api {get} /users/self Get current user information and latest posts
     * @apiName SelfUser
     * @apiGroup Users
     *
     * @apiParam {Number} page Page number for pagination.
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
     * @apiParam {Number} page Page number for pagination.
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
        ], 201);
    }
}
