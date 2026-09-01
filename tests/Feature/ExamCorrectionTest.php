<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Correttezza e prestazioni della correzione (ExamQuizController::correctAnswer).
 * Ogni test qui dentro fallisce sul codice precedente allo Step 5.
 */
class ExamCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private User $docente;

    private User $studente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docente = User::factory()->teacher()->create();
        $this->studente = User::factory()->student()->create();
    }

    public function test_la_correzione_non_esegue_una_query_sui_quiz_per_ogni_risposta(): void
    {
        $exam = Exam::factory()->closed()->create();
        $quizzes = Quiz::factory()->closed()->count(5)->create(['answer' => true, 'points' => 2]);
        $exam->quiz()->attach($quizzes->pluck('id'));
        $exam->user()->attach($this->studente->id, ['created_at' => now(), 'updated_at' => now()]);

        foreach ($quizzes as $quiz) {
            UserAnswer::create([
                'exam_id' => $exam->id,
                'quiz_id' => $quiz->id,
                'user_id' => $this->studente->id,
                'answer' => true,
            ]);
        }

        DB::enableQueryLog();

        $this->actingAs($this->docente)->post(route('display.users.answerP'), [
            'exam_id' => $exam->id,
            'user_id' => $this->studente->id,
        ]);

        $queryPerQuizzes = collect(DB::getQueryLog())
            ->filter(fn ($q) => str_contains(strtolower($q['query']), 'quizzes'))
            ->count();

        DB::disableQueryLog();

        $this->assertSame(
            1,
            $queryPerQuizzes,
            'La correzione esegue una query sui quiz per ogni risposta invece di caricarli una sola volta.'
        );
    }

    public function test_ricorreggere_dopo_aver_cambiato_la_risposta_giusta_azzera_il_punteggio_della_risposta(): void
    {
        $exam = Exam::factory()->closed()->create();
        $quiz = Quiz::factory()->closed()->create(['answer' => true, 'points' => 7]);
        $exam->quiz()->attach($quiz->id);
        $exam->user()->attach($this->studente->id, ['created_at' => now(), 'updated_at' => now()]);

        $userAnswer = UserAnswer::create([
            'exam_id' => $exam->id,
            'quiz_id' => $quiz->id,
            'user_id' => $this->studente->id,
            'answer' => true,
        ]);

        $this->actingAs($this->docente)->post(route('display.users.answerP'), [
            'exam_id' => $exam->id,
            'user_id' => $this->studente->id,
        ]);

        $this->assertSame(7, $userAnswer->fresh()->points);

        // Il docente si accorge di un errore nella domanda e cambia la risposta giusta:
        // la risposta dello studente, che prima era corretta, ora non lo e' piu'.
        $quiz->update(['answer' => false]);

        $this->actingAs($this->docente)->post(route('display.users.answerP'), [
            'exam_id' => $exam->id,
            'user_id' => $this->studente->id,
        ]);

        $this->assertSame(
            0,
            $userAnswer->fresh()->points,
            'Il punteggio della singola risposta e\' rimasto quello della correzione precedente.'
        );
    }
}
