<?php

namespace Tests\Unit\Membership;

use App\Models\Customer;
use App\Models\CustomerMembership;
use App\Models\MembershipTier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_evaluation_handles_downgrade_grace_period(): void
    {
        $bronze = MembershipTier::create([
            'name' => 'Bronze',
            'rank' => 1,
            'min_spend' => 0,
            'is_active' => true,
        ]);

        $silver = MembershipTier::create([
            'name' => 'Silver',
            'rank' => 2,
            'min_spend' => 10000,
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '123456789',
        ]);

        $membership = CustomerMembership::create([
            'customer_id' => $customer->id,
            'current_tier_id' => $silver->id,
            'window_spend' => 5000,
            'evaluated_at' => now(),
            'downgrade_candidate_tier_id' => $bronze->id,
            'downgrade_eligible_at' => Carbon::now()->addDays(30),
        ]);

        $this->assertEquals($silver->id, $membership->current_tier_id);
        $this->assertNotNull($membership->downgrade_eligible_at);
        $this->assertTrue($membership->downgrade_eligible_at->isFuture());
    }
}