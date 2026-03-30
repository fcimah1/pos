<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->bothify('BR-###'),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'tax_rate' => 0.00,
            'is_active' => true,
        ];
    }
}
