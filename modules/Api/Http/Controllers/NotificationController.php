<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Api\Http\Resources\Notifications\NotificationResource;
use Modules\Api\Notifications\Push\UserSubscribed;

class NotificationController extends Controller
{
    /**
     * @api {get} /notifications Notifications
     * @apiName IndexNotifications
     * @apiGroup Notifications
     *
     * @apiUse Token
     * @apiSuccess {Object[]} notifications Notifications.
     * @apiSuccess {Number} notifications.id Notification id.
     * @apiSuccess {String} notifications.type Notification type [PostLiked, PostCommented, UserSubscribed].
     * @apiSuccess {Date} notifications.date Notification date.
     * @apiSuccess {Object[]} notifications.user The user who caused the event.
     * @apiSuccess {Number} notifications.user.id User id.
     * @apiSuccess {String} notifications.user.name User name.
     * @apiSuccess {String} notifications.user.nickname User nickname.
     * @apiSuccess {String} notifications.user.avatar User avatar.
     * @apiSuccess {Object[]} notifications.post Post related to event.
     * @apiSuccess {Number} notifications.post.id Post id.
     * @apiSuccess {String} notifications.post.thumbnail Post thumbnail.
     * @apiSuccess {Number} notifications.comment.id Comment id.
     * @apiSuccess {Number} notifications.comment.user_id Comment author id.
     * @apiSuccess {String} notifications.comment.text Comment description.
     * @apiSuccess {String} notifications.comment.date Comment date.
     * @apiUse Pagination
     */
    public function index(): JsonResponse
    {
        $user = \Auth::user();

        $notifications = $user->notifications()->simplePaginate(10);
        $user->notifications()->update(['read_at' => now()]);

        $items = collect($notifications->items());

        return response()->json([
            'notifications' => array_values(
                $items->filter(function (DatabaseNotification $n) use ($items): bool {
                    $duplicate = $items
                        ->where('type', '=', $n->type)
                        ->where('data.user.id', '=', $n->data['user']['id'])
                        ->where('id', '!=', $n->id)
                        ->first();

                    $types = [UserSubscribed::class];

                    return !(
                        $duplicate
                        && in_array($duplicate->type, $types, true)
                        && $duplicate->created_at > $n->created_at
                    );
                })
                    ->map(function (DatabaseNotification $n): NotificationResource {
                        $className = $n->data['resourceClass'];
                        $basicClass = NotificationResource::class;
                        if (
                            class_exists($className)
                            && ($resource = new $className($n))
                            && ($className === $basicClass || is_subclass_of($resource, $basicClass))
                        ) {
                            return $resource;
                        }
                    })
                    ->toArray()
            ),
            'hasMorePages' => $notifications->hasMorePages()
        ]);
    }

    /**
     * @api {post} /profile/fcm Update firebase token
     * @apiName UpdateFcmToken
     * @apiGroup Notifications
     *
     * @apiUse Token
     *
     * @apiParam {String} token Fcm token.
     *
     * @apiSuccess {String} message Is token saved message.
     */
    public function fcm(Request $request): JsonResponse
    {
        $this->validate($request, ['token' => ['required', 'string']]);

        \Auth::user()->update(['fcm_token' => $request->input('token')]);

        return response()->json([
            'message' => 'FCM token has been updated.'
        ]);
    }
}
