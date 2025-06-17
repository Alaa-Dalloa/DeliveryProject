<?php

namespace App\Http\Middleware;

use Closure;

class ResturantMangerMiddlware
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasRole('Restaurant_manager')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}