<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // Return a 401 Unauthorized response if not authenticated
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has the required role
        if (!$user->hasRole($roles)) {
            // If the user does not have the required role, return a 403 Forbidden response
            return response()->json(['message' => 'Forbidden: Insufficient permissions'], 403);
        }

        // Allow the request to proceed
        return $next($request);
    }
}
