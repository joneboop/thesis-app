<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrganizationUserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('org.manage'); // admin+ only

        $org = $request->user()->currentOrganization;

        $users = $org->users()
            ->select('users.id', 'users.name', 'users.email')
            ->withPivot('role')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->pivot->role,
            ]);

        return Inertia::render('Settings/Users', [
            'users' => $users,
            'roles' => Roles::ALL,
        ]);
    }

public function update(Request $request, User $user)
{
    $this->authorize('org.manage');

    $data = $request->validate([
        'role' => ['required', Rule::in(Roles::ALL)],
    ]);

    $org = $request->user()->currentOrganization;

    abort_unless($org->users()->where('users.id', $user->id)->exists(), 404);

    // 🔐 Only owner can assign owner role
    if ($data['role'] === Roles::OWNER) {
        $this->authorize('org.owner');
    }

    $org->users()->updateExistingPivot($user->id, [
        'role' => $data['role'],
    ]);

    return back()->with('success', 'Role updated');
}

    public function destroy(Request $request, User $user)
    {
        $this->authorize('org.manage');

        $org = $request->user()->currentOrganization;

        abort_unless($org->users()->where('users.id', $user->id)->exists(), 404);

        // Prevent removing yourself (recommended)
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot remove yourself.']);
        }

        $org->users()->detach($user->id);

        // If removed user had this as current org, they’ll be fixed by EnsureCurrentOrganization next request
        return back()->with('success', 'User removed');
    }
}