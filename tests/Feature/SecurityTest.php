<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Copre le falle sfruttabili da uno studente autenticato individuate in fase di
 * ricognizione. Ogni test qui dentro fallisce sul codice precedente allo
 * Step 1: sono la prova che la correzione serve, non un contorno.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $docente;

    private User $studente;

    private User $altroStudente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docente = User::factory()->teacher()->create();
        $this->studente = User::factory()->student()->create();
        $this->altroStudente = User::factory()->student()->create();
    }

    // -----------------------------------------------------------------
    // 1. Privilege escalation dal profilo
    // -----------------------------------------------------------------

    public function test_uno_studente_non_puo_promuoversi_a_docente_dal_profilo(): void
    {
        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'isTeacher' => 1,
        ]);

        $this->assertFalse(
            (bool) $this->studente->fresh()->isTeacher,
            'Uno studente e\' riuscito a promuoversi docente scrivendo isTeacher nel form del profilo.'
        );
    }

    public function test_la_registrazione_non_accetta_il_ruolo_docente(): void
    {
        $this->post(route('register.custom'), [
            'name' => 'Tizio',
            'email' => 'tizio@mexam.test',
            'password' => 'password123',
            'isTeacher' => 1,
        ]);

        $this->assertFalse(
            (bool) User::where('email', 'tizio@mexam.test')->value('isTeacher'),
            'La registrazione ha permesso di autodichiararsi docente.'
        );
    }

    // -----------------------------------------------------------------
    // 2. Leak delle risposte corrette
    // -----------------------------------------------------------------

    public function test_uno_studente_non_puo_leggere_i_quiz_di_una_libreria(): void
    {
        $library = Library::factory()->create();

        $this->actingAs($this->studente)
            ->get(route('libraries.quiz.exam', $library->id))
            ->assertForbidden();
    }

    public function test_l_endpoint_dei_quiz_non_espone_le_risposte_corrette(): void
    {
        $library = Library::factory()->create();
        $quiz = Quiz::factory()->closed()->create();
        $library->quiz()->attach($quiz->id);

        $response = $this->actingAs($this->docente)
            ->get(route('libraries.quiz.exam', $library->id))
            ->assertOk();

        $payload = $response->json();

        $this->assertNotEmpty($payload);
        $this->assertArrayNotHasKey('answer', $payload[0], 'La risposta corretta e\' finita nel JSON.');
        $this->assertArrayNotHasKey('answer_text', $payload[0], 'La risposta corretta e\' finita nel JSON.');
    }

    // -----------------------------------------------------------------
    // 3. IDOR sui risultati altrui
    // -----------------------------------------------------------------

    public function test_uno_studente_non_puo_vedere_il_compito_di_un_altro(): void
    {
        $exam = Exam::factory()->closed()->create();

        $this->actingAs($this->studente)
            ->get(route('display.users.answer', [
                'iduser' => $this->altroStudente->id,
                'idexam' => $exam->id,
            ]))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_stampare_il_compito_di_un_altro(): void
    {
        $exam = Exam::factory()->closed()->create();

        $this->actingAs($this->studente)
            ->get(route('print.exam', [
                'idexam' => $exam->id,
                'iduser' => $this->altroStudente->id,
            ]))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_vedere_l_elenco_dei_risultati(): void
    {
        $exam = Exam::factory()->closed()->create();

        $this->actingAs($this->studente)
            ->get(route('show.users.results.index', $exam->id))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_valutare_un_esame(): void
    {
        $exam = Exam::factory()->closed()->create();

        $this->actingAs($this->studente)
            ->post(route('display.users.answerP'), [
                'exam_id' => $exam->id,
                'user_id' => $this->altroStudente->id,
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------
    // 4. Azioni distruttive senza controllo di ruolo
    // -----------------------------------------------------------------

    public function test_uno_studente_non_puo_cancellare_un_esame(): void
    {
        $exam = Exam::factory()->create();

        $this->actingAs($this->studente)
            ->delete(route('exam.destroy', $exam->id))
            ->assertForbidden();

        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    }

    public function test_uno_studente_non_puo_modificare_un_esame(): void
    {
        $exam = Exam::factory()->create(['exam_name' => 'Originale']);

        $this->actingAs($this->studente)
            ->put(route('exam.update', $exam->id), ['exam_name' => 'Manomesso'])
            ->assertForbidden();

        $this->assertSame('Originale', $exam->fresh()->exam_name);
    }

    public function test_uno_studente_non_puo_staccare_un_quiz_da_un_esame(): void
    {
        $exam = Exam::factory()->create();
        $quiz = Quiz::factory()->create();
        $exam->quiz()->attach($quiz->id);

        $this->actingAs($this->studente)
            ->delete(route('exam.quiz.destroy', $quiz->id))
            ->assertForbidden();

        $this->assertSame(1, $exam->quiz()->count());
    }

    // -----------------------------------------------------------------
    // 5. Rotte senza alcun middleware
    // -----------------------------------------------------------------

    public function test_la_lista_esami_non_e_pubblica(): void
    {
        // Senza `auth` la view eseguiva Auth::user()->isTeacher su null:
        // errore 500 per un visitatore non autenticato.
        $this->get(route('exam.list'))->assertRedirect(route('login'));
    }

    public function test_la_lista_librerie_non_e_pubblica(): void
    {
        $this->get(route('libraryquiz.list'))->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------
    // 6. Archivio domande accessibile agli studenti
    //    (emerso durante lo Step 1, non era nel report iniziale)
    // -----------------------------------------------------------------

    public function test_uno_studente_non_puo_sfogliare_l_archivio_dei_quiz(): void
    {
        // La voce "Quiz List" era nel menu di tutti e la view mostra una
        // colonna "Answer": bastava un clic per vedere ogni domanda con la
        // sua risposta corretta.
        $this->actingAs($this->studente)
            ->get(route('quiz.list'))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_cercare_nei_quiz(): void
    {
        $this->actingAs($this->studente)
            ->get(route('quiz.search', ['question' => 'a']))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_vedere_i_quiz_di_una_libreria(): void
    {
        $library = Library::factory()->create();

        $this->actingAs($this->studente)
            ->get(route('library.quiz', $library->id))
            ->assertForbidden();
    }

    public function test_uno_studente_non_puo_stampare_la_traccia_in_bianco(): void
    {
        $exam = Exam::factory()->create();

        $this->actingAs($this->studente)
            ->get(route('print.blankexam', $exam->id))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------
    // 7. PDF scritti nella webroot
    // -----------------------------------------------------------------

    public function test_la_stampa_non_lascia_file_nella_webroot(): void
    {
        $exam = Exam::factory()->closed()->create();
        $exam->quiz()->attach(Quiz::factory()->create()->id);

        $prima = glob(public_path('pdf/*.pdf')) ?: [];

        $this->actingAs($this->docente)
            ->get(route('print.blankexam', $exam->id))
            ->assertOk();

        $dopo = glob(public_path('pdf/*.pdf')) ?: [];

        $this->assertSame(
            $prima,
            $dopo,
            'La generazione del PDF ha scritto un file scaricabile senza autenticazione.'
        );
    }
}
