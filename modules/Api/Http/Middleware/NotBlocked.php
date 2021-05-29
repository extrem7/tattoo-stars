<?php

namespace Modules\Api\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotBlocked
{
    public function handle(Request $request, \Closure $next)
    {
        $post = $request->route()->parameter('post');
        $user = $request->route()->parameter('user');

        $id = $post !== null ? $post->user_id : $user->id;

        abort_if(
            \Auth::user()->blockers()->where('id', '=', $id)->exists(),
            Response::HTTP_FORBIDDEN,
            'This user blocked you.'
        );

        return $next($request);
    }
}
