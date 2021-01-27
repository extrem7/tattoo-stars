<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcceptLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $domain = Str::of($request->getHost());
        if ($domain->contains('api.') && $request->hasHeader('Accept-Language')) {
            $language = $request->header('Accept-Language');
            if (in_array($language, ['ru', 'en'])) {
                app()->setLocale($request->header('Accept-Language'));
            }
        }

        return $next($request);
    }
}
