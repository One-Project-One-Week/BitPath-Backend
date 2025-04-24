<?php

namespace Database\Factories;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanRequest>
 */
class PlanRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'roadmap_id' => Roadmap::inRandomOrder()->first()->id,
            'type' => $this->faker->randomElement(['deadline', 'tpd']),
            'duration' => $this->faker->randomElement(['15 min', '30 min', '45 min', '1 hour']),
            'days' => $this->faker->numberBetween(1, 45),
        ];
    }
}
