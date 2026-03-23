<?php

namespace Tests\Feature\Membership;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_page_loads_for_authorized_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('membership.customers.index'));

        $response->assertStatus(200);
    }
}