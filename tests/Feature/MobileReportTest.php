<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Administration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MobileReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_report()
    {
        Storage::fake('public');

        // Crear usuario y administración asociados
        $admin = Administration::factory()->create();
        $user = User::factory()->create([
            'role' => 'citizen',
            'administration_id' => $admin->id,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        // Simular imagen subida
        $image = UploadedFile::fake()->image('photo.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/reports', [
                             'title' => 'Animal abandonado en la carretera',
                             'description' => 'Parece que lleva varias horas en el mismo sitio.',
                             'latitude' => 39.4699,
                             'longitude' => -0.3763,
                             'administration_id' => $admin->id,
                             'image' => $image,
                         ]);

                         $response->assertStatus(201)
                         ->assertJsonStructure([
                             'message',
                             'report' => [
                                 'id',
                                 'title',
                                 'description',
                                 'latitude',
                                 'longitude',
                                 'status',
                                 'user_id',
                                 'administration_id',
                                 'image_path',
                                 'created_at',
                             ]
                         ]);
                         $response->assertJson([
                            'message' => 'Denuncia creada con éxito'
                        ]);
                        
                
    }
}
