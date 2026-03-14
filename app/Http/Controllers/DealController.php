<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('org.read');

        $deals = Deal::query()
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Deals/Index', [
            'deals' => $deals,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('org.write');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['open', 'won', 'lost'])],
        ]);

        Deal::create([
            'organization_id' => $request->user()->current_organization_id,
            'title' => $data['title'],
            'value' => (float)($data['value'] ?? 0),
            'currency' => 'EUR',
            'status' => $data['status'] ?? 'open',
        ]);

        return back()->with('success', 'Deal created');
    }

    public function update(Request $request, Deal $deal)
    {
        $this->authorize('org.write');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['open', 'won', 'lost'])],
        ]);

        $deal->update([
            'title' => $data['title'],
            'value' => (float)($data['value'] ?? 0),
            'currency' => 'EUR',
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Deal updated');
    }

    public function destroy(Deal $deal)
    {
        $this->authorize('org.write');

        $deal->delete();

        return back()->with('success', 'Deal deleted');
    }
}