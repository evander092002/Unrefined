<?php

namespace Database\Factories;
use App\Models\Train;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Train>
 */
class TrainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'motion_name' => $this->faker->word,
            'threshold' => $this->faker->randomFloat(2, 0, 100),
            'level' => $this->faker->randomElement(['normal', 'warning', 'danger']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
