<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Database\Factories\QuizFactory;
use Illuminate\Database\Seeder;

/**
 * Prima creava 50 quiz con materia `faker->word()`, cioe' 50 materie diverse:
 * nessuna libreria o esame poteva raggrupparli in modo sensato.
 * Ora i quiz sono distribuiti sulle materie condivise da tutta la demo.
 */
class QuizzesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (QuizFactory::SUBJECTS as $subject) {
            Quiz::factory()->count(8)->closed()->subject($subject)->create();
            Quiz::factory()->count(4)->open()->subject($subject)->create();
        }
    }
}
