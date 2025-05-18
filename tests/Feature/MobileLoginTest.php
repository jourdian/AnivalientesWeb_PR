<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_login_and_receive_token()
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'email' => 'prueba@email.com',
            'password' => Hash::make('secret123'),
            'role' => 'citizen',
        ]);

        // Enviar petición de login
        $response = $this->postJson('/api/login', [
            'email' => 'prueba@email.com',
            'password' => 'secret123',
        ]);

        // Verificar que se devuelve un token y status 200
        $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'email',
                'name'
            ]
        ]);

    }

    public function test_login_fails_with_invalid_credentials()
    {
        // Usuario real
        User::factory()->create([
            'email' => 'invalido@email.com',
            'password' => Hash::make('correcto'),
            'role' => 'citizen',
        ]);

        // Intentar con contraseña incorrecta
        $response = $this->postJson('/api/login', [
            'email' => 'invalido@email.com',
            'password' => 'mal',
        ]);

        $response->assertStatus(401);
    }
}
