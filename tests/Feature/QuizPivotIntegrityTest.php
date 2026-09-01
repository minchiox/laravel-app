<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integrita' delle relazioni quiz<->esame/libreria e chiusura del mass
 * assignment residuo sull'id. Ogni test qui dentro fallisce sul codice
 * precedente allo Step 3.
 */
class QuizPivotIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $docente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docente = User::factory()->teacher()->create();
    }

    // -----------------------------------------------------------------
    // 1. Detach che scollegava il quiz da OGNI esame/libreria
    // -----------------------------------------------------------------

    public function test_rimuovere_un_quiz_da_un_esame_non_lo_rimuove_dagli_altri_esami(): void
    {
        $quiz = Quiz::factory()->create();
        $esameA = Exam::factory()->create(['user_id' => $this->docente->id]);
        $esameB = Exam::factory()->create();
        $esameA->quiz()->attach($quiz->id);
        $esameB->quiz()->attach($quiz->id);

        $this->actingAs($this->docente)
            ->delete(route('exam.quiz.destroy', ['idexam' => $esameA->id, 'idquiz' => $quiz->id]))
            ->assertOk();

        $this->assertSame(0, $esameA->quiz()->count());
        $this->assertSame(1, $esameB->quiz()->count(), 'Il quiz e\' sparito anche dall\'esame non toccato.');
    }

    public function test_rimuovere_un_quiz_da_un_esame_aggiorna_il_punteggio_totale(): void
    {
        $quizDaTenere = Quiz::factory()->create(['points' => 4]);
        $quizDaRimuovere = Quiz::factory()->create(['points' => 6]);
        $esame = Exam::factory()->create(['user_id' => $this->docente->id]);
        $esame->quiz()->attach([$quizDaTenere->id, $quizDaRimuovere->id]);

        // Assegnazione diretta, non mass assignment: total_points e' escluso
        // dai $fillable apposta perche' e' calcolato dai quiz associati.
        $esame->total_points = 10;
        $esame->save();

        $this->actingAs($this->docente)
            ->delete(route('exam.quiz.destroy', ['idexam' => $esame->id, 'idquiz' => $quizDaRimuovere->id]));

        $this->assertSame(4, $esame->fresh()->total_points);
    }

    public function test_rimuovere_un_quiz_da_una_libreria_non_lo_rimuove_dalle_altre_librerie(): void
    {
        $quiz = Quiz::factory()->create();
        $libreriaA = Library::factory()->create(['user_id' => $this->docente->id]);
        $libreriaB = Library::factory()->create();
        $libreriaA->quiz()->attach($quiz->id);
        $libreriaB->quiz()->attach($quiz->id);

        $this->actingAs($this->docente)
            ->delete(route('library.quiz.destroy', ['idlibrary' => $libreriaA->id, 'idquiz' => $quiz->id]))
            ->assertOk();

        $this->assertSame(0, $libreriaA->quiz()->count());
        $this->assertSame(1, $libreriaB->quiz()->count(), 'Il quiz e\' sparito anche dalla libreria non toccata.');
    }

    // -----------------------------------------------------------------
    // 2. Chiave primaria mass assignable residua
    // -----------------------------------------------------------------

    public function test_l_id_di_una_libreria_non_e_mass_assignable(): void
    {
        $esistente = Library::factory()->create();

        $this->actingAs($this->docente)->post(route('library.store'), [
            'library_name' => 'Nuova libreria',
            'library_subject' => 'Matematica',
            'library_difficulty' => 'easy',
            'id' => $esistente->id,
        ]);

        $nuova = Library::where('library_name', 'Nuova libreria')->firstOrFail();

        $this->assertNotSame(
            $esistente->id,
            $nuova->id,
            'Il campo id inviato dal form ha sovrascritto una libreria esistente.'
        );
    }

    public function test_l_id_non_e_fra_i_fillable_di_userAnswer(): void
    {
        $this->assertNotContains('id', (new UserAnswer())->getFillable());
    }
}
