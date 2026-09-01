<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Le materie usate da tutta la demo: libraries, quiz ed esami si agganciano
     * a questa lista, cosi' i dati generati restano coerenti fra loro.
     */
    public const SUBJECTS = ['Matematica', 'Storia', 'Informatica'];

    public const DIFFICULTIES = ['easy', 'medium', 'hard'];

    protected $model = Quiz::class;

    public function definition(): array
    {
        // Un quiz e' o a risposta chiusa (vero/falso -> `answer`) o a risposta
        // aperta (testo -> `answer_text`), mai entrambe: e' l'invariante su cui
        // si basano le view e la correzione.
        return $this->faker->boolean(60)
            ? $this->closedState()
            : $this->openState();
    }

    /** Domanda vero/falso. */
    public function closed(): static
    {
        return $this->state(fn () => $this->closedState());
    }

    /** Domanda a risposta testuale. */
    public function open(): static
    {
        return $this->state(fn () => $this->openState());
    }

    public function subject(string $subject): static
    {
        return $this->state(fn () => ['subject' => $subject]);
    }

    public function difficulty(string $difficulty): static
    {
        return $this->state(fn () => ['difficulty' => $difficulty]);
    }

    private function closedState(): array
    {
        return $this->baseState() + [
            'answer' => $this->faker->boolean(),
            'answer_text' => null,
        ];
    }

    private function openState(): array
    {
        return $this->baseState() + [
            'answer' => null,
            'answer_text' => $this->faker->words(3, true),
        ];
    }

    private function baseState(): array
    {
        return [
            'question' => rtrim($this->faker->sentence(), '.').'?',
            'subject' => $this->faker->randomElement(self::SUBJECTS),
            'difficulty' => $this->faker->randomElement(self::DIFFICULTIES),
            // era numberBetween(1, 100): un punteggio a tre cifre per una
            // singola domanda rendeva i totali di esempio poco leggibili
            'points' => $this->faker->numberBetween(1, 10),
        ];
    }
}
