<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CitizensSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Generando ciudadanos de prueba...');

        $ciudadanos = [
            ['first_name' => 'Laura',   'last_name' => 'Serrano Ruiz'],
            ['first_name' => 'Pedro',   'last_name' => 'Navarro Gil'],
            ['first_name' => 'Lucía',   'last_name' => 'Mendoza Pérez'],
            ['first_name' => 'Óscar',   'last_name' => 'Gómez Cano'],
            ['first_name' => 'Ana',     'last_name' => 'Martínez Flores'],
            ['first_name' => 'Javier',  'last_name' => 'Sánchez Vera'],
            ['first_name' => 'Marina',  'last_name' => 'López Vives'],
            ['first_name' => 'Alberto', 'last_name' => 'Romero Díaz'],
            ['first_name' => 'Irene',   'last_name' => 'García León'],
            ['first_name' => 'Sergio',  'last_name' => 'Vázquez Rubio'],
        ];

        foreach ($ciudadanos as $persona) {
            $email = $this->generarEmail($persona['first_name'], $persona['last_name']);

            User::factory()->create([
                'first_name' => $persona['first_name'],
                'last_name' => $persona['last_name'],
                'email' => $email,
                'password' => Hash::make('password'),
                'phone' => '600' . random_int(100000, 999999),
                'street' => 'Calle ' . Str::title(Str::random(6)) . ', ' . random_int(1, 100),
                'city' => 'Ciudadano City',
                'province' => 'Ciudadanía',
                'firebase_uid' => null,
                'role' => 'citizen',
                'position' => null,
                'administration_id' => null,
            ]);

            $this->command->info("✅ {$persona['first_name']} {$persona['last_name']} → {$email}");
        }

        // Ciudadano especial UOCVLC
        User::firstOrCreate(
            ['email' => 'alumnouocvlc@email.com'],
            [
                'first_name' => 'Alumno',
                'last_name' => 'Universitat Oberta de Catalunya VLC',
                'password' => Hash::make('password'),
                'phone' => '600222222',
                'street' => 'C/ Falsa, 123',
                'city' => 'Quart de Poblet',
                'province' => 'Valencia',
                'firebase_uid' => null,
                'role' => 'citizen',
                'position' => null,
                'administration_id' => null,
            ]
        );

        // Ciudadano especial UOCBCN
        User::firstOrCreate(
            ['email' => 'alumnouocbcn@email.com'],
            [
                'first_name' => 'Alumno',
                'last_name' => 'Universitat Oberta de Catalunya BCN',
                'password' => Hash::make('password'),
                'phone' => '600333333',
                'street' => 'C/ Inventada, 456',
                'city' => 'L\'Hospitalet de Llobregat',
                'province' => 'Barcelona',
                'firebase_uid' => null,
                'role' => 'citizen',
                'position' => null,
                'administration_id' => null,
            ]
        );

        $this->command->info("Usuarios especiales creados: alumnouocvlc@email.com y alumnouocbcn@email.com");
        $this->command->info('Ciudadanos creados correctamente.');
    }

    private function generarEmail(string $firstName, string $lastName): string
    {
        $first = Str::slug($firstName, '.');
        $last = Str::slug($lastName, '.');
        return "{$first}.{$last}@ciudadano.com";
    }
}
