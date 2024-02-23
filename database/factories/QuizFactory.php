<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;


        public function definition(): array
    {
        $answer = null;
        $answerText = null;

        // Determina casualmente se popolare 'answer' o 'answer_text'
        if ($this->faker->boolean) {
            $answer = $this->faker->randomElement(['1', '0']);
        } else {
            $answerText = $this->faker->sentence;
        }

        return [
            'question' => $this->faker->sentence,
            'answer' => $answer,
            'answer_text' => $answerText,
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'subject' => $this->faker->word,
            'points' => $this->faker->numberBetween(1, 100),
        ];
    }
}

