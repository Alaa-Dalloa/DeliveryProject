<?php

namespace App\Http\Middleware;

use Closure;

class DeliveryWorkerMiddelware
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasRole('Delivery_worker')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}