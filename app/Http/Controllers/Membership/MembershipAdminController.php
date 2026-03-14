<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Controller;
use App\Models\MembershipSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipAdminController extends Controller
{
    public function settings()
    {
        $settings = MembershipSetting::firstOrCreate(['id' => 1], [
            'window_days' => 365,
            'grace_days'  => 60,
        ]);

        return Inertia::render('Membership/Admin/Settings', [
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'window_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'grace_days'  => ['required', 'integer', 'min:0', 'max:3650'],
        ]);

        $settings = MembershipSetting::firstOrCreate(['id' => 1]);
        $settings->update($data);

        return back()->with('success', 'Membership settings updated.');
    }

    public function recalculateAll()
    {
        // Later: dispatch job/service e.g. EvaluateMembershipsJob::dispatch();
        return back()->with('success', 'Recalculation queued (stub).');
    }
}