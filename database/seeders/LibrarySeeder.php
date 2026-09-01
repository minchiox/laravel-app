<?php

namespace Database\Seeders;

use App\Models\Library;
use App\Models\Quiz;
use Database\Factories\QuizFactory;
use Illuminate\Database\Seeder;

/**
 * Prima creava 2 librerie vuote e scollegate dai quiz: la pagina "Libraries
 * List" mostrava righe che, aperte, non contenevano nulla.
 */
class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        foreach (QuizFactory::SUBJECTS as $subject) {
            $library = Library::factory()->subject($subject)->create();

            $quizIds = Quiz::where('subject', $subject)->pluck('id');

            $library->quiz()->syncWithoutDetaching(
                $quizIds->mapWithKeys(fn (int $id) => [
                    $id => ['created_at' => now(), 'updated_at' => now()],
                ])->all()
            );
        }
    }
}
