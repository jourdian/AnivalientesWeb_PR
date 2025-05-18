<?php

namespace Tests\Feature;

use App\Models\Administration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_their_profile_data()
    {
        // Crear una administración y un usuario ciudadano
        $admin = Administration::factory()->create([
            'name' => 'Ayuntamiento de UOC'
        ]);

        $user = User::factory()->create([
            'role' => 'citizen',
            'administration_id' => $admin->id,
            'first_name' => 'Pepe',
            'last_name' => 'Grillo',
            'email' => 'pepe@example.com',
            'phone' => '600123456',
            'street' => 'Calle Sol, 3',
            'city' => 'Cheste',
            'province' => 'Valencia',
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'first_name' => 'Pepe',
                     'last_name' => 'Grillo',
                     'email' => 'pepe@example.com',
                     'phone' => '600123456',
                     'address' => 'Calle Sol, 3, Cheste, Valencia',
                     'administration_name' => 'Ayuntamiento de UOC',
                 ]);
    }
}
