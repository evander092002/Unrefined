<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_captured' => now(),
            'motion_detected' => $this->faker->word,
            'level' => $this->faker->randomElement(['normal', 'warning', 'danger']),
            'status' => $this->faker->randomElement(['approved', 'ignored']),
            'snapshot' => $this->faker->imageUrl(),
            'user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
