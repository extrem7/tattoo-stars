<?php

namespace Modules\Api\Http\Middleware;

use App\Models\Chat\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsChatParticipant
{
    public function handle(Request $request, \Closure $next)
    {
        /* @var $chat Chat */
        $chat = $request->route()->parameter('chat');
        if ($chat->participants()->where('id', '=', $request->user()->id)->exists()) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'You don\'t have access to this chat.');
    }
}
