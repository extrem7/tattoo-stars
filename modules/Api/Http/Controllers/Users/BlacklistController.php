<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\SubscriberResource;

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
    public function blacklist(): JsonResponse
    {
        $blacklist = \Auth::user()->blacklist()->with(['avatarMedia'])->get(['id', 'name', 'nickname']);

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
    public function toggle(User $user): JsonResponse
    {
        $blocker = \Auth::user();

        abort_if($user->id === $blocker->id, 403, 'You cannot add self to blacklist.');

        $changes = $blocker->blacklist()->toggle([
            $user->id => ['blocked_at' => \DB::raw('NOW()')]
        ]);
        $action = !empty($changes['attached']) ? 'added to blacklist' : 'removed from blacklist';

        return response()->json([
            'message' => ucfirst($user->nickname) . " has been $action."
        ], 201);
    }
}
