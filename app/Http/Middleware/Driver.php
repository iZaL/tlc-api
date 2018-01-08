<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Driver
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if ($user->active && $user->driver->active) {
            return $next($request);
        }

        return response()->json(
            ['success' => false, 'message' => trans('general.invalid_request'), 'type' => 'invalid_request'],
            403
        );

//        return $next($request);
    }
}
