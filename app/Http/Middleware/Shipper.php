<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Shipper
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->shipper && auth()->user()->shipper->active) {
            return $next($request);
        }

        return response()->json(
            ['success' => false, 'message' => trans('general.invalid_request'), 'type' => 'invalid_request'],
            403
        );
    }
}
