<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdministrationFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Ayuntamiento de ' . $this->faker->lastName() . ' de ' . $this->faker->word();
        $slug = Str::slug($name);

        return [
            'name' => $name,
            'address' => $this->faker->streetName() . ', ' . $this->faker->buildingNumber(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'phone' => '900' . $this->faker->numberBetween(100000, 999999),
            'email' => "info@{$slug}.instituciones.test", 
            'logo_path' => null,
        ];
    }
}
