<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    public function definition(): array
    {
        $subject = $this->faker->randomElement(QuizFactory::SUBJECTS);

        return [
            // era fake()->name(): il nome di una persona come nome di un esame
            'exam_name' => "Verifica di {$subject}",
            ...$this->openState(),
        ];
    }

    /** Esame in corso: gli studenti possono accedere adesso. */
    public function open(): static
    {
        return $this->state(fn () => $this->openState());
    }

    /** Esame gia' concluso: utile per mostrare la pagina dei risultati. */
    public function closed(): static
    {
        return $this->state(fn () => [
            'startAt' => now()->subDays(7),
            'dueAt' => now()->subDays(6),
        ]);
    }

    /** Esame non ancora iniziato. */
    public function upcoming(): static
    {
        return $this->state(fn () => [
            'startAt' => now()->addDays(3),
            'dueAt' => now()->addDays(3)->addHours(2),
        ]);
    }

    private function openState(): array
    {
        return [
            'startAt' => now()->subHour(),
            'dueAt' => now()->addDays(7),
        ];
    }
}
