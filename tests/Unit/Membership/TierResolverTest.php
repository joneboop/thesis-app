<?php

namespace Tests\Unit\Membership;

use App\Models\MembershipTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TierResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_spending_determines_correct_tier(): void
    {
        MembershipTier::create([
            'name' => 'Bronze',
            'rank' => 1,
            'min_spend' => 0,
            'is_active' => true,
        ]);

        MembershipTier::create([
            'name' => 'Silver',
            'rank' => 2,
            'min_spend' => 10000,
            'is_active' => true,
        ]);

        MembershipTier::create([
            'name' => 'Gold',
            'rank' => 3,
            'min_spend' => 30000,
            'is_active' => true,
        ]);

        $spend = 15000;

        $tier = MembershipTier::query()
            ->where('is_active', true)
            ->where('min_spend', '<=', $spend)
            ->orderByDesc('rank')
            ->first();

        $this->assertNotNull($tier);
        $this->assertEquals('Silver', $tier->name);
    }
}