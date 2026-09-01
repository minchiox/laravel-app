<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Library>
 */
class LibraryFactory extends Factory
{
    public function definition(): array
    {
        $subject = $this->faker->randomElement(QuizFactory::SUBJECTS);
        $difficulty = $this->faker->randomElement(QuizFactory::DIFFICULTIES);

        return [
            'user_id' => User::factory()->teacher(),
            // era fake()->name(), cioe' il nome di una persona come nome di una
            // libreria di quiz
            'library_name' => "{$subject} · livello {$difficulty}",
            // era randomElement(['easy','medium','hard']): copia-incolla della
            // riga difficulty, quindi la materia conteneva una difficolta'
            'library_subject' => $subject,
            'library_difficulty' => $difficulty,
        ];
    }

    public function subject(string $subject): static
    {
        return $this->state(fn (array $attributes) => [
            'library_subject' => $subject,
            'library_name' => "{$subject} · livello {$attributes['library_difficulty']}",
        ]);
    }
}
