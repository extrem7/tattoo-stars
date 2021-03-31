<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * @api {post} /users/:id/subscribe Toggle subscription
     * @apiName ToggleSubscription
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status.
     */
    public function __invoke(User $user): JsonResponse
    {
        $subscriber = \Auth::user();

        abort_if($user->id === $subscriber->id, 403, 'You cannot subscribe to self.');

        $changes = $subscriber->subscriptions()->toggle([
            $user->id => ['subscribed_at' => \DB::raw('NOW()')]
        ]);
        $action = !empty($changes['attached']) ? 'subscribed to' : 'unsubscribed from';

        return response()->json([
            'message' => "You has been $action $user->nickname."
        ], 201);
    }
}
