<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\CurrentOrganization;

class EnsureCurrentOrganization
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            if (!$user->current_organization_id) {
                $firstOrgId = $user->organizations()->orderBy('organizations.id')->value('organizations.id');
                if ($firstOrgId) {
                    $user->current_organization_id = $firstOrgId;
                    $user->save();
                }
            }
        }
        app(CurrentOrganization::class)->id = $user?->current_organization_id;
        return $next($request);
    }
}