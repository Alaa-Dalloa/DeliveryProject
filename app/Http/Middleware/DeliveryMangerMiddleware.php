<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DeliveryMangerMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();

        // Check if the authenticated user has the 'Delivery_manager' role
        if ($user->hasRole('Delivery_manger')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}