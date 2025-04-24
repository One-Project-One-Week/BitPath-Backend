<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Roadmap>
 */
class RoadmapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prompt' => $this->faker->randomElement(['frontend developer roadmap', 'backend developer roadmap', 'full stack developer roadmap']),
            'title' => $this->faker->randomElement(['Frontend Developer Roadmap', 'Backend Developer Roadmap', 'Full Stack Developer Roadmap']),
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }
}
