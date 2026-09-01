<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integrita' della transazione di consegna: e' il percorso su cui si fonda il
 * valore del prodotto, ma non aveva nessuna difesa.
 *
 * Ogni test qui dentro fallisce sul codice precedente allo Step 2.
 */
class ExamSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $studente;

    protected function setUp(): void
    {
        parent::setUp();
        $this->studente = User::factory()->student()->create();
    }

    private function esameConQuiz(string $stato = 'open'): Exam
    {
        $exam = Exam::factory()->{$stato}()->create();
        $exam->quiz()->attach(Quiz::factory()->count(3)->closed()->create()->pluck('id'));

        return $exam;
    }

    /** @return array<string, mixed> */
    private function risposteDi(Exam $exam): array
    {
        $payload = ['exam_id' => $exam->id];

        foreach ($exam->quiz as $quiz) {
            $payload['answer'.$quiz->id] = '1';
        }

        return $payload;
    }

    // --- Finestra temporale ------------------------------------------

    public function test_non_si_puo_consegnare_un_esame_non_ancora_iniziato(): void
    {
        $exam = $this->esameConQuiz('upcoming');

        $this->actingAs($this->studente)
            ->post(route('store.user.answer'), $this->risposteDi($exam))
            ->assertForbidden();

        $this->assertSame(0, UserAnswer::count());
    }

    public function test_non_si_puo_consegnare_un_esame_gia_chiuso(): void
    {
        $exam = $this->esameConQuiz('closed');

        $this->actingAs($this->studente)
            ->post(route('store.user.answer'), $this->risposteDi($exam))
            ->assertForbidden();

        $this->assertSame(0, UserAnswer::count());
    }

    // --- Consegna singola --------------------------------------------

    public function test_la_consegna_registra_le_risposte_e_iscrive_lo_studente(): void
    {
        $exam = $this->esameConQuiz();

        $this->actingAs($this->studente)
            ->post(route('store.user.answer'), $this->risposteDi($exam))
            ->assertRedirect();

        $this->assertSame(3, UserAnswer::count());
        $this->assertTrue($exam->user()->where('user_id', $this->studente->id)->exists());
    }

    public function test_non_si_puo_consegnare_due_volte(): void
    {
        $exam = $this->esameConQuiz();
        $payload = $this->risposteDi($exam);

        $this->actingAs($this->studente)->post(route('store.user.answer'), $payload);
        $this->assertSame(3, UserAnswer::count());

        $this->actingAs($this->studente)
            ->post(route('store.user.answer'), $payload)
            ->assertForbidden();

        $this->assertSame(3, UserAnswer::count(), 'La seconda consegna ha aggiunto altre risposte.');
    }

    // --- Aprire non equivale a consegnare -----------------------------

    public function test_aprire_l_esame_senza_consegnare_non_blocca_un_secondo_accesso(): void
    {
        $exam = $this->esameConQuiz();

        // Prima l'iscrizione avveniva all'apertura: chiudere la pagina
        // significava restare fuori dall'esame per sempre.
        $this->actingAs($this->studente)->get(route('exam.access', $exam->id))->assertOk();
        $this->actingAs($this->studente)->get(route('exam.access', $exam->id))->assertOk();
    }

    public function test_non_si_puo_riaprire_un_esame_gia_consegnato(): void
    {
        $exam = $this->esameConQuiz();

        $this->actingAs($this->studente)->post(route('store.user.answer'), $this->risposteDi($exam));

        $this->actingAs($this->studente)
            ->get(route('exam.access', $exam->id))
            ->assertRedirect();
    }

    // --- Le risposte salvate sono solo quelle dell'esame --------------

    public function test_non_vengono_salvate_risposte_di_quiz_estranei_all_esame(): void
    {
        $exam = $this->esameConQuiz();
        $estraneo = Quiz::factory()->closed()->create();

        $payload = $this->risposteDi($exam);
        $payload['answer'.$estraneo->id] = '1';

        $this->actingAs($this->studente)->post(route('store.user.answer'), $payload);

        $this->assertSame(3, UserAnswer::count());
        $this->assertDatabaseMissing('user_answers', ['quiz_id' => $estraneo->id]);
    }

    public function test_l_esame_deve_esistere(): void
    {
        $this->actingAs($this->studente)
            ->post(route('store.user.answer'), ['exam_id' => 9999])
            ->assertSessionHasErrors('exam_id');
    }
}
