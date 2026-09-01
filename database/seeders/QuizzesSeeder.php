<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
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
        // Senza user_id esplicito, la factory ne genera uno nuovo per ogni
        // quiz: il seed si ritrovava con decine di docenti fantasma invece
        // che con l'unico docente della demo.
        $docenteId = User::where('email', UserSeeder::TEACHER_EMAIL)->value('id');

        foreach (QuizFactory::SUBJECTS as $subject) {
            Quiz::factory()->count(8)->closed()->subject($subject)->create(['user_id' => $docenteId]);
            Quiz::factory()->count(4)->open()->subject($subject)->create(['user_id' => $docenteId]);
        }
    }
}
