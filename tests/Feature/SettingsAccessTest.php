<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_access_settings()
    {
        $admin = Administration::factory()->create();
        $user = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        $response = $this->actingAs($user, 'web')->get('/settings');
        $response->assertStatus(200);
    }

    public function test_citizen_user_cannot_access_settings()
    {
        $user = User::factory()->create([
            'role' => 'citizen',
        ]);

        $response = $this->actingAs($user, 'web')->get('/settings');
        $response->assertStatus(403);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }
}
