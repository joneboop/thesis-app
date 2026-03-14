<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\Roles;

class RequireOrgRole
{
    public function handle(Request $request, Closure $next, string $minRole)
    {
        $user = $request->user();

        if (!$user || !$user->hasOrgRole($minRole)) {
            abort(403);
        }

        return $next($request);
    }
}