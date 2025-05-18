<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_access_dashboard_data()
    {
        $admin = Administration::factory()->create();
        $institutional = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        Report::factory()->count(3)->create(['status' => 'pending']);
        Report::factory()->count(2)->create(['status' => 'resolved']);

        $response = $this->actingAs($institutional)
                         ->get('/admin/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'statusDistribution',
                     'reportsByDay',
                 ]);

        $data = $response->json();

        $this->assertArrayHasKey('pending', $data['statusDistribution']);
        $this->assertArrayHasKey('resolved', $data['statusDistribution']);
        $this->assertIsArray($data['reportsByDay']);
    }
}
