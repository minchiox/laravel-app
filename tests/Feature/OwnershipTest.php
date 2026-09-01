<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * IDOR docente-docente: prima della colonna user_id e delle Policy (Fase 2,
 * Step P1), qualunque docente autenticato poteva leggere, modificare,
 * cancellare e persino rivalutare il materiale e i compiti di un altro
 * docente, cambiando l'id nell'URL. Il middleware isTeacher filtra solo il
 * ruolo, non la proprieta': ogni test qui dentro fallisce senza le Policy.
 */
class OwnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $docenteA;

    private User $docenteB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docenteA = User::factory()->teacher()->create();
        $this->docenteB = User::factory()->teacher()->create();
    }

    // -----------------------------------------------------------------
    // Esami
    // -----------------------------------------------------------------

    public function test_un_docente_non_puo_modificare_lesame_di_un_altro(): void
    {
        $exam = Exam::factory()->create(['user_id' => $this->docenteA->id, 'exam_name' => 'Originale']);

        $this->actingAs($this->docenteB)->put(route('exam.update', $exam->id), [
            'exam_name' => 'Manomesso',
            'startAt' => $exam->startAt,
            'dueAt' => $exam->dueAt,
        ])->assertForbidden();

        $this->assertSame('Originale', $exam->fresh()->exam_name);
    }

    public function test_un_docente_non_puo_cancellare_lesame_di_un_altro(): void
    {
        $exam = Exam::factory()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->delete(route('exam.destroy', $exam->id))
            ->assertForbidden();

        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    }

    public function test_un_docente_non_puo_vedere_i_risultati_dellesame_di_un_altro(): void
    {
        $exam = Exam::factory()->closed()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->get(route('show.users.results.index', $exam->id))
            ->assertForbidden();
    }

    public function test_un_docente_non_puo_rivalutare_lesame_di_un_altro(): void
    {
        $exam = Exam::factory()->closed()->create(['user_id' => $this->docenteA->id]);
        $studente = User::factory()->student()->create();

        $this->actingAs($this->docenteB)->post(route('display.users.answerP'), [
            'exam_id' => $exam->id,
            'user_id' => $studente->id,
        ])->assertForbidden();
    }

    public function test_un_docente_non_puo_stampare_la_traccia_dellesame_di_un_altro(): void
    {
        $exam = Exam::factory()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->get(route('print.blankexam', $exam->id))
            ->assertForbidden();
    }

    public function test_un_docente_non_puo_agganciare_un_quiz_allesame_di_un_altro(): void
    {
        $exam = Exam::factory()->create(['user_id' => $this->docenteA->id]);
        $quiz = Quiz::factory()->create(['user_id' => $this->docenteB->id]);

        $this->actingAs($this->docenteB)->post(route('examquiz.store'), [
            'exam_id' => $exam->id,
            'quiz_id' => $quiz->id,
        ])->assertForbidden();

        $this->assertSame(0, $exam->quiz()->count());
    }

    // -----------------------------------------------------------------
    // Quiz
    // -----------------------------------------------------------------

    public function test_un_docente_non_puo_modificare_il_quiz_di_un_altro(): void
    {
        $quiz = Quiz::factory()->closed()->create(['user_id' => $this->docenteA->id, 'question' => 'Originale?']);

        $this->actingAs($this->docenteB)->put(route('quiz.update', $quiz->id), [
            'question' => 'Manomesso?',
            'answer-type' => 'close',
            'answer' => '1',
            'subject' => $quiz->subject,
            'difficulty' => $quiz->difficulty,
            'points' => $quiz->points,
        ])->assertForbidden();

        $this->assertSame('Originale?', $quiz->fresh()->question);
    }

    public function test_un_docente_non_puo_cancellare_il_quiz_di_un_altro(): void
    {
        $quiz = Quiz::factory()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->delete(route('quiz.destroy', $quiz->id))
            ->assertForbidden();

        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id]);
    }

    public function test_larchivio_quiz_non_mostra_quelli_di_un_altro_docente(): void
    {
        Quiz::factory()->create(['user_id' => $this->docenteA->id, 'question' => 'Domanda di A?']);
        Quiz::factory()->create(['user_id' => $this->docenteB->id, 'question' => 'Domanda di B?']);

        $response = $this->actingAs($this->docenteA)->get(route('quiz.list'));

        $response->assertSee('Domanda di A?');
        $response->assertDontSee('Domanda di B?');
    }

    // -----------------------------------------------------------------
    // Librerie
    // -----------------------------------------------------------------

    public function test_un_docente_non_puo_modificare_la_libreria_di_un_altro(): void
    {
        $library = Library::factory()->create(['user_id' => $this->docenteA->id, 'library_name' => 'Originale']);

        $this->actingAs($this->docenteB)->put(route('library.update', $library->id), [
            'library_name' => 'Manomessa',
            'library_subject' => $library->library_subject,
            'library_difficulty' => $library->library_difficulty,
        ])->assertForbidden();

        $this->assertSame('Originale', $library->fresh()->library_name);
    }

    public function test_un_docente_non_puo_cancellare_la_libreria_di_un_altro(): void
    {
        $library = Library::factory()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->delete(route('library.destroy', $library->id))
            ->assertForbidden();

        $this->assertDatabaseHas('libraries', ['id' => $library->id]);
    }

    public function test_un_docente_non_puo_vedere_i_quiz_di_una_libreria_di_un_altro(): void
    {
        $library = Library::factory()->create(['user_id' => $this->docenteA->id]);

        $this->actingAs($this->docenteB)
            ->get(route('library.quiz', $library->id))
            ->assertForbidden();
    }

    public function test_un_docente_non_puo_agganciare_il_quiz_di_un_altro_alla_propria_libreria(): void
    {
        $quiz = Quiz::factory()->create(['user_id' => $this->docenteA->id]);
        $libreriaPropria = Library::factory()->create(['user_id' => $this->docenteB->id]);

        $this->actingAs($this->docenteB)->post(route('libraryquiz.store'), [
            'library_id' => $libreriaPropria->id,
            'quiz_id' => $quiz->id,
        ])->assertForbidden();

        $this->assertSame(0, $libreriaPropria->quiz()->count());
    }

    // -----------------------------------------------------------------
    // Assegnazione del proprietario alla creazione
    // -----------------------------------------------------------------

    public function test_creare_un_esame_lo_assegna_a_chi_lo_crea(): void
    {
        $this->actingAs($this->docenteA)->post(route('exam.store'), [
            'exam_name' => 'Prova di proprieta\'',
            'startAt' => now()->addHour()->format('Y-m-d H:i:s'),
            'dueAt' => now()->addHours(2)->format('Y-m-d H:i:s'),
        ]);

        $this->assertSame(
            $this->docenteA->id,
            Exam::where('exam_name', 'Prova di proprieta\'')->value('user_id')
        );
    }

    public function test_creare_un_quiz_lo_assegna_a_chi_lo_crea(): void
    {
        $this->actingAs($this->docenteA)->post(route('quiz.store'), [
            'question' => 'Domanda di proprieta\'?',
            'answer-type' => 'close',
            'answer' => '1',
            'subject' => 'Matematica',
            'difficulty' => 'easy',
            'points' => 5,
        ]);

        $this->assertSame(
            $this->docenteA->id,
            Quiz::where('question', 'Domanda di proprieta\'?')->value('user_id')
        );
    }

    public function test_creare_una_libreria_la_assegna_a_chi_la_crea(): void
    {
        $this->actingAs($this->docenteA)->post(route('library.store'), [
            'library_name' => 'Libreria di proprieta\'',
            'library_subject' => 'Matematica',
            'library_difficulty' => 'easy',
        ]);

        $this->assertSame(
            $this->docenteA->id,
            Library::where('library_name', 'Libreria di proprieta\'')->value('user_id')
        );
    }
}
