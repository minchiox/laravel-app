<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use Database\Factories\QuizFactory;
use Illuminate\Database\Seeder;

/**
 * Prima creava un solo esame senza quiz e senza partecipanti: aprendolo si
 * vedeva una pagina vuota e la schermata dei risultati non aveva nulla da
 * mostrare.
 *
 * Ora genera tre esami che coprono i tre stati del ciclo di vita, di cui uno
 * gia' svolto da due studenti, cosi' la demo mostra anche correzione e stampa.
 */
class ExamSeeder extends Seeder
{
    public function run(): void
    {
        [$matematica, $storia, $informatica] = QuizFactory::SUBJECTS;

        $docenteId = User::where('email', UserSeeder::TEACHER_EMAIL)->value('id');

        // In corso: e' l'esame che uno studente puo' aprire e consegnare subito.
        $this->exam($matematica, $docenteId, fn () => Exam::factory()->open());

        // Non ancora iniziato: verifica che l'accesso anticipato venga bloccato.
        $this->exam($informatica, $docenteId, fn () => Exam::factory()->upcoming());

        // Concluso e gia' consegnato da due studenti: popola la pagina risultati.
        $concluso = $this->exam($storia, $docenteId, fn () => Exam::factory()->closed());
        $this->submitAnswers($concluso);
    }

    /**
     * @param  callable():\Illuminate\Database\Eloquent\Factories\Factory  $factory
     */
    private function exam(string $subject, int $docenteId, callable $factory): Exam
    {
        $exam = $factory()->create(['exam_name' => "Verifica di {$subject}", 'user_id' => $docenteId]);

        $quizzes = Quiz::where('subject', $subject)->take(6)->get();

        $exam->quiz()->syncWithoutDetaching(
            $quizzes->mapWithKeys(fn (Quiz $quiz) => [
                $quiz->id => ['created_at' => now(), 'updated_at' => now()],
            ])->all()
        );

        // total_points non e' in $fillable, quindi va assegnato direttamente.
        $exam->total_points = $quizzes->sum('points');
        $exam->save();

        return $exam;
    }

    /**
     * Due studenti consegnano l'esame: uno risponde bene a tutto, l'altro no.
     * Le risposte restano non corrette (points a null) perche' la correzione e'
     * l'azione che si vuole poter dimostrare dalla UI del docente.
     */
    private function submitAnswers(Exam $exam): void
    {
        $students = User::where('isTeacher', false)->take(2)->get();
        $quizzes = $exam->quiz()->get();

        foreach ($students as $index => $student) {
            $rispondeBene = $index === 0;

            $exam->user()->syncWithoutDetaching([
                $student->id => ['created_at' => now(), 'updated_at' => now()],
            ]);

            foreach ($quizzes as $quiz) {
                UserAnswer::create([
                    'user_id' => $student->id,
                    'exam_id' => $exam->id,
                    'quiz_id' => $quiz->id,
                    ...$this->answerFor($quiz, $rispondeBene),
                ]);
            }
        }
    }

    /** @return array{answer: bool|null, answer_text: string|null} */
    private function answerFor(Quiz $quiz, bool $corretta): array
    {
        if ($quiz->answer_text !== null) {
            return [
                'answer' => null,
                'answer_text' => $corretta ? $quiz->answer_text : 'risposta non pertinente',
            ];
        }

        return [
            'answer' => $corretta ? $quiz->answer : ! $quiz->answer,
            'answer_text' => null,
        ];
    }
}
