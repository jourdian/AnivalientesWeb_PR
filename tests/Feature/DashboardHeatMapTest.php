<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardHeatmapTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_access_heatmap_data()
    {
        // Crear administración y usuario institucional
        $admin = Administration::factory()->create();
        $institutional = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        // Crear denuncias distribuidas por varios días del mes actual
        Carbon::setTestNow(Carbon::create(2025, 5, 10));
        Report::factory()->count(2)->create(['created_at' => now()->subDays(1)]);
        Report::factory()->count(3)->create(['created_at' => now()->subDays(2)]);
        Report::factory()->count(1)->create(['created_at' => now()->subDays(10)]);

        // Consultar heatmap
        $response = $this->actingAs($institutional)
            ->get('/admin/api/reports/heatmap?month=5&year=2025');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'name',
                         'reports',
                     ]
                 ]);

        // Verificar que se devuelven fechas y cantidades correctas
        $data = $response->json();

        $this->assertNotEmpty($data);
        $this->assertTrue(collect($data)->contains(fn($day) => $day['reports'] === 3));
        $this->assertTrue(collect($data)->contains(fn($day) => $day['reports'] === 2));
        $this->assertTrue(collect($data)->contains(fn($day) => $day['reports'] === 1));
    }
}
