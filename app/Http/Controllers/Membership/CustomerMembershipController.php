<?php

namespace App\Http\Controllers\Membership;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\CustomerMembership;

class CustomerMembershipController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $customers = Customer::query()
            ->when($q, fn($query) => $query
                ->where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"))
            ->with(['membership.currentTier'])
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Membership/Customers/Index', [
            'customers' => $customers,
            'filters' => ['q' => $q],
        ]);
    }

public function show(Customer $customer)
{
    $customer->load([
        'membership.currentTier',
        'membership.overrideTier',
        'membership.downgradeCandidateTier',
        'membershipEvents' => fn($q) => $q->latest()->limit(50),
    ]);

    return Inertia::render('Membership/Customers/Show', [
        'customer' => $customer,
        'tiers' => MembershipTier::orderBy('rank')->get(['id', 'name', 'rank']),
    ]);
}

    public function recalculate(Customer $customer)
    {
        // Later: call evaluator service for single customer
        return back()->with('success', 'Recalculation queued (stub).');
    }

    public function setOverride(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'override_tier_id' => ['required', 'exists:membership_tiers,id'],
            'override_until'   => ['nullable', 'date'],
            'override_reason'  => ['nullable', 'string', 'max:500'],
        ]);

        $membership = $customer->membership()->firstOrCreate([]);

        $membership->update([
            'override_tier_id' => $data['override_tier_id'],
            'override_until'   => $data['override_until'] ?? null,
            'override_reason'  => $data['override_reason'] ?? null,
        ]);

        return back()->with('success', 'Override set.');
    }

    public function clearOverride(Customer $customer)
    {
        $membership = $customer->membership()->firstOrCreate([]);

        $membership->update([
            'override_tier_id' => null,
            'override_until'   => null,
            'override_reason'  => null,
        ]);

        return back()->with('success', 'Override cleared.');
    }


public function createDemoCustomer($tier)
{
    $spend = match ($tier) {
        'bronze' => rand(500, 3000),
        'silver' => rand(10000, 20000),
        'gold' => rand(30000, 50000),
        default => rand(1000, 5000),
    };

    $customer = Customer::create([
        'first_name' => ucfirst($tier),
        'last_name' => Str::random(4),
        'email' => Str::random(5) . '@demo.com',
        'phone' => '000000000',
    ]);

    Invoice::create([
        'customer_id' => $customer->id,
        'total_net' => $spend,
        'status' => 'paid',
        'issued_at' => now(),
        'paid_at' => now(),
    ]);

    $assignedTier = MembershipTier::query()
        ->where('is_active', true)
        ->where('min_spend', '<=', $spend)
        ->orderByDesc('rank')
        ->first();

    CustomerMembership::create([
        'customer_id' => $customer->id,
        'current_tier_id' => $assignedTier?->id,
        'window_spend' => $spend,
        'evaluated_at' => now(),
    ]);

    return redirect()->route('membership.customers.index')
        ->with('success', ucfirst($tier) . ' demo customer created.');
}
}