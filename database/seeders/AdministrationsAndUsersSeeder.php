<?php

namespace Database\Seeders;

use App\Models\Administration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdministrationsAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌐 Creando instituciones y usuarios institucionales...');

        // Lista de instituciones ficticias
        $instituciones = [
            [
                'name' => 'Ayuntamiento de Villatrasto del Monte',
                'city' => 'Villatrasto',
                'responsable' => ['first_name' => 'Roberto', 'last_name' => 'Gómez Fernández'],
                'becario' => ['first_name' => 'Carmen', 'last_name' => 'López Rivas'],
            ],
            [
                'name' => 'Consistorio de Chorrazuela de Abajo',
                'city' => 'Chorrazuela',
                'responsable' => ['first_name' => 'Luis', 'last_name' => 'Martínez Soto'],
                'becario' => ['first_name' => 'Patricia', 'last_name' => 'Ruiz Toral'],
            ],
            [
                'name' => 'Ayuntamiento de Santa Otilia del Desvío',
                'city' => 'Santa Otilia',
                'responsable' => ['first_name' => 'Beatriz', 'last_name' => 'Giménez Valls'],
                'becario' => ['first_name' => 'Julián', 'last_name' => 'Nogales Pérez'],
            ],
            [
                'name' => 'Administración Local de Pueblón Perdido',
                'city' => 'Pueblón Perdido',
                'responsable' => ['first_name' => 'Almudena', 'last_name' => 'Hernández León'],
                'becario' => ['first_name' => 'Carlos', 'last_name' => 'Fernández Ruiz'],
            ],
        ];

        collect($instituciones)->each(function ($data) {
            $slug = Str::slug($data['city']);
            $dominio = "{$slug}.com";
            $emailInstitucion = "info@{$dominio}";

            $admin = Administration::factory()->create([
                'name' => $data['name'],
                'city' => $data['city'],
                'email' => $emailInstitucion,
            ]);

            $responsable = User::factory()->create([
                'administration_id' => $admin->id,
                'role' => 'institutional',
                'position' => 'Responsable de Cosas Muy Serias',
                'first_name' => $data['responsable']['first_name'],
                'last_name' => $data['responsable']['last_name'],
                'email' => $this->generarEmailUsuario($data['responsable']['first_name'], $data['responsable']['last_name'], $dominio),
            ]);

            $becario = User::factory()->create([
                'administration_id' => $admin->id,
                'role' => 'institutional',
                'position' => 'Becario/a sin Café',
                'first_name' => $data['becario']['first_name'],
                'last_name' => $data['becario']['last_name'],
                'email' => $this->generarEmailUsuario($data['becario']['first_name'], $data['becario']['last_name'], $dominio),
            ]);

            $this->command->info("✅ {$admin->name} ({$emailInstitucion}):");
            $this->command->info("   - {$responsable->position}: {$responsable->email}");
            $this->command->info("   - {$becario->position}: {$becario->email}");
        });

        // Institución fija: UOCVLC (Valencia)
        $uocvlc = Administration::firstOrCreate(
            ['email' => 'info@uocvlc.com'],
            [
                'name' => 'UOC Valencia',
                'address' => 'C/ UOC VLC, 1',
                'city' => 'Valencia',
                'province' => 'Valencia',
                'phone' => '900123456',
                'latitude' => 39.4947639,
                'longitude' => -0.6857103,
                'logo_path' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'uocvlc@uoc.com'],
            [
                'first_name' => 'Universitat',
                'last_name' => 'Oberta de Catalunya VLC',
                'password' => bcrypt('password'),
                'phone' => '600000000',
                'street' => 'C/ UOC VLC, 1',
                'city' => 'Valencia',
                'province' => 'Valencia',
                'role' => 'institutional',
                'position' => 'Responsable UOCVLC',
                'administration_id' => $uocvlc->id,
            ]
        );

        // Institución fija: UOCBCN (Barcelona)
        $uocbcn = Administration::firstOrCreate(
            ['email' => 'info@uocbcn.com'],
            [
                'name' => 'UOC Barcelona',
                'address' => 'C/ UOC BCN, 1',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'phone' => '900987654',
                'latitude' => 41.3874,
                'longitude' => 2.1686,
                'logo_path' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'uocbcn@uoc.com'],
            [
                'first_name' => 'Universitat',
                'last_name' => 'Oberta de Catalunya BCN',
                'password' => bcrypt('password'),
                'phone' => '600111111',
                'street' => 'C/ UOC BCN, 1',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'role' => 'institutional',
                'position' => 'Responsable UOCBCN',
                'administration_id' => $uocbcn->id,
            ]
        );

        $this->command->info("🎓 Instituciones UOC creadas: UOCVLC y UOCBCN.");
    }

    private function generarEmailUsuario(string $firstName, string $lastName, string $dominio): string
    {
        $first = Str::slug($firstName, '.');
        $last = Str::slug($lastName, '.');
        return "{$first}.{$last}@{$dominio}";
    }
}
