<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('org.read');

        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'first_name');      // allowed: first_name, email, company, created_at
        $dir  = $request->query('dir', 'asc');              // asc|desc

        $allowedSorts = ['first_name', 'email', 'company', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'first_name';
        if (!in_array($dir, ['asc', 'desc'], true)) $dir = 'asc';

        $contacts = Contact::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('company', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'dir' => $dir,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('org.write');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['nullable', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:60'],
            'company'    => ['nullable', 'string', 'max:120'],
        ]);

        // Always set org explicitly (bulletproof)
        $data['organization_id'] = $request->user()->current_organization_id;

        Contact::create($data);

        return back()->with('success', 'Contact created');
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('org.write');

        // Contact is already org-scoped by your global scope, so if it's not in current org → 404

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['nullable', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:60'],
            'company'    => ['nullable', 'string', 'max:120'],
        ]);

        $contact->update($data);

        return back()->with('success', 'Contact updated');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('org.write');

        $contact->delete(); // soft delete

        return back()->with('success', 'Contact deleted');
    }
}