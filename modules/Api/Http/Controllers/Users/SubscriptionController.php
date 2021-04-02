<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\SearchRequest;
use Modules\Api\Http\Resources\SubscriberResource;

class SubscriptionController extends Controller
{
    protected int $limit = 18;

    /**
     * @api {post} /users/:id/subscribers User subscribers.
     * @apiName UserSubscribers
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {Object[]} subscribers Subscribers.
     * @apiSuccess {Integer} subscribers.id Subscriber id.
     * @apiSuccess {String} subscribers.icon Subscriber icon.
     * @apiSuccess {String} subscribers.name Subscriber name.
     * @apiSuccess {String} subscribers.nickname Subscriber nickname.
     *
     * @apiUse Pagination
     */
    public function subscribers(User $user, SearchRequest $request): JsonResponse
    {
        $query = $request->input('query');

        $subscribers = $this->getSubscriptions($user, 'subscribers', $query);

        return response()->json([
            'subscribers' => SubscriberResource::collection($subscribers),
            'hasMorePages' => $subscribers->hasMorePages()
        ], 201);
    }

    /**
     * @api {post} /users/:id/subscriptions User subscriptions.
     * @apiName UserSubscriptions
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {Object[]} subscriptions Subscriptions.
     * @apiSuccess {Integer} subscriptions.id Subscription id.
     * @apiSuccess {String} subscriptions.icon Subscription icon.
     * @apiSuccess {String} subscriptions.name Subscription name.
     * @apiSuccess {String} subscriptions.nickname Subscription nickname.
     *
     * @apiUse Pagination
     */
    public function subscriptions(User $user, SearchRequest $request): JsonResponse
    {
        $query = $request->input('query');

        $subscriptions = $this->getSubscriptions($user, 'subscriptions', $query);

        return response()->json([
            'subscriptions' => SubscriberResource::collection($subscriptions),
            'hasMorePages' => $subscriptions->hasMorePages()
        ], 201);
    }

    /**
     * @api {post} /users/:id/subscribe Toggle subscription
     * @apiName ToggleSubscription
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status.
     */
    public function toggle(User $user): JsonResponse
    {
        $subscriber = \Auth::user();

        abort_if($user->id === $subscriber->id, 403, 'You cannot subscribe to self . ');

        $changes = $subscriber->subscriptions()->toggle([
            $user->id => ['subscribed_at' => \DB::raw('NOW()')]
        ]);
        $action = !empty($changes['attached']) ? 'subscribed to' : 'unsubscribed from';

        return response()->json([
            'message' => "You has been $action $user->nickname."
        ], 201);
    }

    protected function getSubscriptions(User $user, string $scope, string $query = null): Paginator
    {
        if (!in_array($scope, ['subscribers', 'subscriptions'])) {
            throw new \InvalidArgumentException;
        }

        /* @var $items User */
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
