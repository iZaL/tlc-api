<?php

namespace App\Http\Middleware;

use Closure;

class Locale
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->has('lang')) {
            app()->setLocale($request->lang);
        }
        return $next($request);
    }
}