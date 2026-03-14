<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipTier;
use App\Models\MembershipSetting;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        // Settings (single row)
        MembershipSetting::updateOrCreate(
            ['id' => 1],
            ['window_days' => 365, 'grace_days' => 60]
        );

        // Tiers
        MembershipTier::updateOrCreate(
            ['rank' => 1],
            ['name' => 'Bronze', 'min_spend' => 0, 'is_active' => true]
        );

        MembershipTier::updateOrCreate(
            ['rank' => 2],
            ['name' => 'Silver', 'min_spend' => 10000, 'is_active' => true]
        );

        MembershipTier::updateOrCreate(
            ['rank' => 3],
            ['name' => 'Gold', 'min_spend' => 30000, 'is_active' => true]
        );
    }
}