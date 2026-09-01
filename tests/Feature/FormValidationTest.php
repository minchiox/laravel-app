<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validazione centralizzata nelle Form Request di Quiz/Exam/Library. Ogni
 * test qui dentro fallisce sul codice precedente allo Step 4, che validava
 * solo 1-2 campi e lasciava passare il resto con $request->all().
 */
class FormValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $docente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docente = User::factory()->teacher()->create();
    }

    // -----------------------------------------------------------------
    // Quiz
    // -----------------------------------------------------------------

    public function test_creare_un_quiz_richiede_risposta_e_punteggio(): void
    {
        $this->actingAs($this->docente)
            ->post(route('quiz.store'), ['question' => 'Quanto fa 2+2?'])
            ->assertSessionHasErrors(['answer-type', 'subject', 'difficulty', 'points']);

        $this->assertSame(0, Quiz::count());
    }

    public function test_si_puo_creare_un_quiz_a_risposta_chiusa(): void
    {
        $this->actingAs($this->docente)->post(route('quiz.store'), [
            'question' => 'Il PHP e\' tipizzato staticamente?',
            'answer-type' => 'close',
            'answer' => '0',
            'subject' => 'Informatica',
            'difficulty' => 'easy',
            'points' => 5,
        ])->assertRedirect();

        $quiz = Quiz::first();

        $this->assertNotNull($quiz);
        $this->assertNull($quiz->answer_text);
        $this->assertFalse($quiz->answer);
    }

    public function test_modificare_un_quiz_da_chiuso_ad_aperto_azzera_la_risposta_booleana(): void
    {
        $quiz = Quiz::factory()->closed()->create(['answer' => true]);

        $this->actingAs($this->docente)->put(route('quiz.update', $quiz->id), [
            'question' => $quiz->question,
            'answer-type' => 'open',
            'answer_text' => 'Risposta aperta',
            'subject' => $quiz->subject,
            'difficulty' => $quiz->difficulty,
            'points' => $quiz->points,
        ]);

        $quiz->refresh();

        $this->assertSame('Risposta aperta', $quiz->answer_text);
        $this->assertNull(
            $quiz->answer,
            'La risposta booleana del vecchio tipo "chiuso" e\' rimasta dopo il cambio a "aperto".'
        );
    }

    // -----------------------------------------------------------------
    // Exam
    // -----------------------------------------------------------------

    public function test_la_data_di_fine_esame_deve_essere_dopo_l_inizio(): void
    {
        $this->actingAs($this->docente)->post(route('exam.store'), [
            'exam_name' => 'Prova',
            'startAt' => '2026-01-10 10:00:00',
            'dueAt' => '2026-01-10 09:00:00',
        ])->assertSessionHasErrors('dueAt');

        $this->assertSame(0, Exam::count());
    }

    public function test_si_puo_creare_un_esame_con_date_valide(): void
    {
        $this->actingAs($this->docente)->post(route('exam.store'), [
            'exam_name' => 'Prova',
            'startAt' => '2026-01-10 09:00:00',
            'dueAt' => '2026-01-10 10:00:00',
        ])->assertRedirect();

        $this->assertSame(1, Exam::count());
    }

    // -----------------------------------------------------------------
    // Library
    // -----------------------------------------------------------------

    public function test_creare_una_libreria_richiede_una_difficolta_valida(): void
    {
        $this->actingAs($this->docente)->post(route('library.store'), [
            'library_name' => 'Libreria',
            'library_subject' => 'Storia',
            'library_difficulty' => 'impossibile',
        ])->assertSessionHasErrors('library_difficulty');

        $this->assertSame(0, Library::count());
    }
}
