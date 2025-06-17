<?php

namespace App\Http\Middleware;

use Closure;

class DeliveryMangerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasRole('Delivery_manger')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}