<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_institutional_user_can_update_a_report()
    {
        // Crear usuario institucional
        $admin = \App\Models\Administration::factory()->create();

        $user = User::factory()->create([
            'role' => 'institutional',
            'administration_id' => $admin->id,
        ]);
        
        // Crear denuncia
        $report = Report::factory()->create([
            'status' => 'pending',
            'severity' => 'medium',
            'response' => null,
        ]);

        // Nuevos datos
        $newStatus = 'resolved';
        $newSeverity = 'high';
        $newResponse = 'Recogida por el servicio veterinario.';

        // Enviar actualización
        $response = $this->actingAs($user, 'web')
        ->withHeaders([
            'Accept' => 'application/json',
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest', 
        ])
        ->put("/reports/{$report->id}", [
            'status' => $newStatus,
            'severity' => $newSeverity,
            'response' => $newResponse,
        ]);
    
    $response->assertRedirect(); 
    
    

        // Refrescar el modelo
        $report->refresh();

        // Verificar cambios
        $this->assertEquals($newStatus, $report->status);
        $this->assertEquals($newSeverity, $report->severity);
        $this->assertEquals($newResponse, $report->response);
    }
}
