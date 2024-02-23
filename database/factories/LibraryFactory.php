<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Library>
 */
class LibraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'library_name' => fake()->name(),
            'library_subject' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'library_difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}
