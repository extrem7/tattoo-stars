<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\SearchRequest;
use Modules\Api\Http\Resources\SubscriberResource;
use Modules\Api\Repositories\UserRepository;
use Modules\Api\Services\UserService;

final class SubscriptionController extends Controller
{
    protected int $limit = 18;

    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @api {get} /users/:id/subscribers User subscribers.
     * @apiName UserSubscribers
     * @apiGroup Users
     *
     * @apiUse Token
     * @apiUse Search
     *
     * @apiSuccess {Object[]} subscribers Subscribers.
     * @apiSuccess {Integer} subscribers.id Subscriber id.
     * @apiSuccess {String} subscribers.icon Subscriber icon.
     * @apiSuccess {String} subscribers.name Subscriber name.
     * @apiSuccess {String} subscribers.nickname Subscriber nickname.
     * @apiSuccess {String} subscribers.location Subscriber location.
     *
     * @apiUse Pagination
     */
    public function subscribers(User $user, SearchRequest $request): JsonResponse
    {
        $subscribers = $this->repository->getSubscriptions($user, 'subscribers', $request->input('query'));

        return response()->json([
            'subscribers' => SubscriberResource::collection($subscribers),
            'hasMorePages' => $subscribers->hasMorePages()
        ]);
    }

    /**
     * @api {get} /users/:id/subscriptions User subscriptions.
     * @apiName UserSubscriptions
     * @apiGroup Users
     *
     * @apiUse Token
     * @apiUse Search
     *
     * @apiSuccess {Object[]} subscriptions Subscriptions.
     * @apiSuccess {Integer} subscriptions.id Subscription id.
     * @apiSuccess {String} subscriptions.icon Subscription icon.
     * @apiSuccess {String} subscriptions.name Subscription name.
     * @apiSuccess {String} subscriptions.nickname Subscription nickname.
     * @apiSuccess {String} subscriptions.location Subscription location.
     *
     * @apiUse Pagination
     */
    public function subscriptions(User $user, SearchRequest $request): JsonResponse
    {
        $subscriptions = $this->repository->getSubscriptions($user, 'subscriptions', $request->input('query'));

        return response()->json([
            'subscriptions' => SubscriberResource::collection($subscriptions),
            'hasMorePages' => $subscriptions->hasMorePages()
        ]);
    }

    /**
     * @api {post} /users/:id/subscribe Toggle subscription
     * @apiName ToggleSubscription
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle message.
     * @apiSuccess {Boolean} subscribed Subscribed status.
     */
    public function toggle(User $user, UserService $service): JsonResponse
    {
        $subscribed = $service->toggleSubscription(\Auth::user(), $user);
        $action = $subscribed ? 'subscribed to' : 'unsubscribed from';

        return response()->json([
            'message' => "You has been $action $user->nickname.",
            'subscribed' => $subscribed
        ]);
    }
}
