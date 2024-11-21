<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $permissionsArray = explode('|', $permissions[0]);

        // Check if user has all required permissions
        $hasAllPermissions = collect($permissionsArray)->every(fn($permission) => $request->user()->can($permission));

        return $hasAllPermissions
            ? $next($request)
            : response()->json(['access-denied' => 'Unauthorized action'], 403);
    }
}
