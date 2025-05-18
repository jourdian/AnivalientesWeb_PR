<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => '600' . $this->faker->numberBetween(100000, 999999),
            'street' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'firebase_uid' => null,
            'role' => 'institutional',
            'position' => $this->faker->randomElement([
                'Responsable de Cosas Muy Serias',
                'Delegada de Asuntos Turbios',
                'Jefa de Desvíos y Rodeos',
                'Encargado de Lo Que Surja',
                'Becario sin Café',
                'Becaria de lo que haya',
                'Becario de Pasillo',
                'Becaria Inexistente',
            ]),
            'administration_id' => null, 
        ];
    }
}
