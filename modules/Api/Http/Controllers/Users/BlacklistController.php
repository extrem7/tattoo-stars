<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\SubscriberResource;
use Modules\Api\Repositories\UserRepository;
use Modules\Api\Services\UserService;

class BlacklistController extends Controller
{
    /**
     * @api {get} /blacklist User blacklist.
     * @apiName UserBlacklist
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {Object[]} blacklist Blacklist.
     * @apiSuccess {Integer} blacklist.id Blocked user id.
     * @apiSuccess {String} blacklist.icon Blocked user icon.
     * @apiSuccess {String} blacklist.name Blocked user name.
     * @apiSuccess {String} blacklist.nickname Blocked user nickname.
     *
     */
    public function blacklist(UserRepository $repository): JsonResponse
    {
        $blacklist = $repository->getBlacklist(\Auth::user());

        return response()->json(SubscriberResource::collection($blacklist));
    }

    /**
     * @api {post} /users/:id/blacklist Toggle blocking
     * @apiName ToggleBlocking
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status.
     */
    public function toggle(User $user, UserService $service): JsonResponse
    {
        $action = $service->toggleBlacklist(\Auth::user(), $user) ? 'added to blacklist' : 'removed from blacklist';

        return response()->json([
            'message' => ucfirst($user->nickname) . " has been $action."
        ], 201);
    }
}
