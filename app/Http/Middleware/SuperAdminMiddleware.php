<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasRole('Super_admin')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}