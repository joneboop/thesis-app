<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipTierController extends Controller
{
    public function index()
    {
        return Inertia::render('Membership/Admin/Tiers/Index', [
            'tiers' => MembershipTier::orderBy('rank')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Membership/Admin/Tiers/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:50'],
            'rank'      => ['required', 'integer', 'min:1', 'max:1000'],
            'min_spend' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        MembershipTier::create($data);

        return redirect()->route('membership.tiers.index')
            ->with('success', 'Tier created.');
    }

    public function edit(MembershipTier $tier)
    {
        return Inertia::render('Membership/Admin/Tiers/Edit', [
            'tier' => $tier,
        ]);
    }

    public function update(Request $request, MembershipTier $tier)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:50'],
            'rank'      => ['required', 'integer', 'min:1', 'max:1000'],
            'min_spend' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $tier->update($data);

        return redirect()->route('membership.tiers.index')
            ->with('success', 'Tier updated.');
    }

    public function destroy(MembershipTier $tier)
    {
        $tier->delete();

        return back()->with('success', 'Tier deleted.');
    }
}