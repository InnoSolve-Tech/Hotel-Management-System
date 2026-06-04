<?php

namespace App\Http\Middleware;

use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next)
    {
        $permissionName = PermissionRegistry::resolvePermissionFromRequest($request);

        if (! $permissionName) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! $user->hasPermission($permissionName)) {
            abort(403, 'You do not have access to this resource.');
        }

        return $next($request);
    }
}
