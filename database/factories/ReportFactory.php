<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Report;
use App\Models\Administration;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $citizen = User::where('role', 'citizen')->inRandomOrder()->first()?->id ?? User::factory();
        $admin = Administration::inRandomOrder()->first()?->id ?? Administration::factory();

        $issues = [
            'una farola fundida',
            'un contenedor quemado',
            'aceras en mal estado',
            'basura acumulada',
            'ruido excesivo por la noche',
            'una fuga de agua',
            'un semáforo que no funciona',
            'grafitis en mobiliario urbano',
            'un árbol caído',
            'una señal de tráfico rota',
        ];

        $locations = [
            'cerca del colegio',
            'junto al parque central',
            'en la calle Mayor',
            'en la plaza del Ayuntamiento',
            'frente al supermercado',
            'en la esquina de mi casa',
            'en la carretera de acceso al pueblo',
            'cerca del centro de salud',
        ];

        $issue = fake()->randomElement($issues);
        $location = fake()->randomElement($locations);

        $description = "He detectado {$issue} {$location}. Solicito intervención municipal.";

        return [
            'user_id' => $citizen,
            'administration_id' => $admin,
            'title' => ucfirst($issue),
            'description' => $description,
            'image_path' => null,
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'latitude' => fake()->latitude(39.4, 39.6),
            'longitude' => fake()->longitude(-0.4, -0.3),
            'status' => fake()->randomElement(['pending', 'reviewing', 'resolved', 'dismissed']),
            'severity' => fake()->randomElement(['low', 'medium', 'high','critical']), 
            'response' => fake()->boolean(40) ? 'Estamos gestionando la incidencia. Gracias por su colaboración.' : null,
        ];
    }
}
