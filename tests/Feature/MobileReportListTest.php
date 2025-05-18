<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileReportListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_see_their_own_reports()
    {
        // Crear dos usuarios distintos
        $user1 = User::factory()->create(['role' => 'citizen']);
        $user2 = User::factory()->create(['role' => 'citizen']);

        // Crear denuncias: 2 para user1, 3 para user2
        Report::factory()->count(2)->create(['user_id' => $user1->id]);
        Report::factory()->count(3)->create(['user_id' => $user2->id]);

        // Autenticación como user1
        $token = $user1->createToken('mobile')->plainTextToken;

        // Solicitud GET
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/reports');

        // Comprobar que devuelve solo las de user1
        $response->assertStatus(200)
                 ->assertJsonCount(2); 
                 
        // Validar estructura de un ítem
        $response->assertJsonStructure([
            [
                'id',
                'title',
                'description',
                'status',
                'created_at',
                'image_url',
                'latitude',
                'longitude',
            ]
        ]);
    }
}
