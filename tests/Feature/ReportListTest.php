<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportListTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_see_reports_list()
    {
        $admin = Administration::factory()->create();

        $user = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);

        $reports = Report::factory()->count(3)->create([
            'administration_id' => $admin->id,
        ]);

        $response = $this->actingAs($user, 'web')
                         ->get('/reports');

        $response->assertStatus(200);

        // Verifica que se carga el componente de Inertia correcto y se reciben las denuncias
        $response->assertInertia(fn ($page) =>
            $page->component('admin/Reports')
                 ->has('reports.data', 3)
                 ->where('reports.data.0.title', $reports[0]->title)
        );
    }

    public function test_citizen_user_cannot_access_reports_list()
    {
        $user = User::factory()->create(['role' => 'citizen']);

        $response = $this->actingAs($user, 'web')
                         ->get('/reports');

        $response->assertStatus(403);
    }
}
