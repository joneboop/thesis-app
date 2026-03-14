<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Gate;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
public function share(Request $request): array
{
    $user = $request->user();

    $org = null;
    $role = null;

    if ($user && $user->current_organization_id) {
        $org = $user->currentOrganization()->first();

        $membership = $user->organizations()
            ->where('organizations.id', $user->current_organization_id)
            ->first();

        $role = $membership?->pivot?->role;
    }

    // Compute permissions from role (this avoids Gate issues while we debug)
    $rank = [
        'readonly' => 10,
        'member'   => 20,
        'admin'    => 30,
        'owner'    => 40,
    ];

    $roleRank = $role ? ($rank[$role] ?? 0) : 0;

    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $user,
            'organization' => $org,
            'role' => $role,
        ],
        'can' => [
            'orgRead' => $roleRank >= 10,
            'orgWrite' => $roleRank >= 20,
            'orgManage' => $roleRank >= 30,
        ],
    ]);
}
}