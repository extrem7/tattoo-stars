<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Api\Http\Resources\Notifications\NotificationResource;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = \Auth::user();

        $notifications = $user->notifications()->simplePaginate(10);
        $user->notifications()->update(['read_at' => now()]);

        return response()->json([
            'notifications' => array_map(
                fn(DatabaseNotification $n): NotificationResource => new $n->data['resourceClass']($n),
                $notifications->items()
            ),
            'hasMorePages' => $notifications->hasMorePages()
        ]);
    }
}
