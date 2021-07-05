<?php

namespace Modules\Api\Http\Middleware;

use App\Models\Chat\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsMessageSender
{
    public function handle(Request $request, \Closure $next)
    {
        /* @var $message Message */
        $message = $request->route()->parameter('message');
        if ($message->user_id === \Auth::id()) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'You aren\'t sender of this message.');
    }
}
