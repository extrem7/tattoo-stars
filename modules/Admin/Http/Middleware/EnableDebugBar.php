<?php

namespace Modules\Admin\Http\Middleware;

use Auth;
use Closure;
use Debugbar;
use Illuminate\Http\Request;

class EnableDebugBar
{
    public function handle(Request $request, Closure $next)
    {
        Debugbar::disable();

        if (Auth::check() && in_array(Auth::getUser()->email, config('admin.debugbar_emails'), true)) {
            Debugbar::enable();
        }

        return $next($request);
    }
}
