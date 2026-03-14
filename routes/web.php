<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ContactController;
use App\Support\Roles;
use App\Http\Controllers\OrganizationUserController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\Membership\MembershipAdminController;
use App\Http\Controllers\Membership\MembershipTierController;
use App\Http\Controllers\Membership\CustomerMembershipController;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'org.role:' . Roles::ADMIN])->group(function () {
    Route::get('/settings/users', fn () => Inertia::render('Settings/Users'));
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
});
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/settings/users', [OrganizationUserController::class, 'index'])->name('org.users.index');
    Route::patch('/settings/users/{user}', [OrganizationUserController::class, 'update'])->name('org.users.update');
    Route::delete('/settings/users/{user}', [OrganizationUserController::class, 'destroy'])->name('org.users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::patch('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
    Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
    Route::patch('/deals/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::delete('/deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');
});

Route::get('/_db', function () {
    return response()->json([
        'default_connection' => config('database.default'),
        'sqlite_database' => config('database.connections.sqlite.database'),
        'absolute_path' => realpath(config('database.connections.sqlite.database')),
        'file_exists' => file_exists(config('database.connections.sqlite.database')),
    ]);
});

Route::prefix('membership')->name('membership.')->group(function () {
    // Admin
    Route::get('/settings', [MembershipAdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [MembershipAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/recalculate', [MembershipAdminController::class, 'recalculateAll'])->name('recalculate.all');

    Route::resource('/tiers', MembershipTierController::class)->except(['show']);

    // Per-customer membership
    Route::get('/customers', [CustomerMembershipController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerMembershipController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/recalculate', [CustomerMembershipController::class, 'recalculate'])->name('customers.recalculate');
    Route::post('/customers/{customer}/override', [CustomerMembershipController::class, 'setOverride'])->name('customers.override');
    Route::delete('/customers/{customer}/override', [CustomerMembershipController::class, 'clearOverride'])->name('customers.override.clear');
});

Route::post('/membership/demo-customer/{tier}', [CustomerMembershipController::class, 'createDemoCustomer'])
    ->name('membership.demo.create');

Route::get('/_me', function () {
    $u = request()->user();

    if (!$u) {
        return response()->json(['logged_in' => false]);
    }

    $currentOrgId = $u->current_organization_id;

    $pivotRole = null;
    if ($currentOrgId) {
        $row = $u->organizations()
            ->where('organizations.id', $currentOrgId)
            ->first();

        $pivotRole = $row?->pivot?->role;
    }

    return response()->json([
        'user_id' => $u->id,
        'email' => $u->email,
        'current_organization_id' => $currentOrgId,
        'current_org_name' => $u->currentOrganization?->name,
        'role_in_current_org' => $pivotRole,
        'org_memberships' => $u->organizations()->get(['organizations.id','organizations.name'])->map(function ($org) {
            return [
                'id' => $org->id,
                'name' => $org->name,
                'role' => $org->pivot->role,
            ];
        }),
    ]);
})->middleware('auth');