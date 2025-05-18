<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'institutional',
        ]);

        $response = $this->actingAs($user)
                         ->get('/dashboard');

        $response->assertStatus(200); 
        $response->assertSee('Dashboard'); 
    }

    public function test_citizen_user_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'citizen',
        ]);

        $response = $this->actingAs($user)
                         ->get('/dashboard');

                         $response->assertStatus(403); 

    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
