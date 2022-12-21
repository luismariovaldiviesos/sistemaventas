<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'businame' => $this->faker->name(),
            'typeidenti' => $this->faker->randomElement(['ci','ruc']),
            'valueidenti' => $this->faker->unique()->ean13(),
            'phone' => $this->faker->numerify('##########'),
            'address' => $this->faker->streetName(),
            'email' => $this->faker->unique()->safeEmail(),
            'notes' => ''
        ];
    }
}
