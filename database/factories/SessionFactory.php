<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class SessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'activated_at' => fake()->dateTimeBetween('-2 month', '-1 month'),
            'appointment' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
