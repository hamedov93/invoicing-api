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
        $activatedAt = fake()->dateTimeBetween('-2 month', '-5 days');
        $appointment = fake()->dateTimeBetween($activatedAt, 'now');

        return [
            'activated_at' => $activatedAt->format('Y-m-d'),
            'appointment' => $appointment->format('Y-m-d'),
        ];
    }
}
