<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (! auth()->check()) {
            // User is not authenticated, you can redirect or return an error response
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if the user has the required permission
        if (! auth()->user()->hasPermissionTo('role-and-permission-management', 'api')) {
            // User does not have the required permission
            return response()->json(
                [

                    'message' => 'You do not have the required permission.',
                ],
                403
            );
        }

        // User has the required permission, proceed with the request
        return $next($request);
    }
}
