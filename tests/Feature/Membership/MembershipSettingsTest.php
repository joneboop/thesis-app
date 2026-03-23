<?php

namespace Tests\Feature\Membership;

use App\Models\MembershipSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_settings_can_be_updated_successfully(): void
    {
        $user = User::factory()->create();

        MembershipSetting::create([
            'window_days' => 365,
            'grace_days' => 60,
        ]);

        $response = $this->actingAs($user)->put(route('membership.settings.update'), [
            'window_days' => 180,
            'grace_days' => 30,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('membership_settings', [
            'window_days' => 180,
            'grace_days' => 30,
        ]);
    }
}