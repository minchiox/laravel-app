<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            ->delete(route('exam.quiz.destroy', ['idexam' => $exam->id, 'idquiz' => $quiz->id]))
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

    // -----------------------------------------------------------------
    // 8. Upload avatar: RCE e XSS via contenuto travestito da immagine
    // -----------------------------------------------------------------

    public function test_un_upload_travestito_da_immagine_non_e_accettato(): void
    {
        Storage::fake('local');

        // GIF89a e' una firma di formato valido ma non e' nell'allow-list:
        // e' il polyglot classico per un upload-to-RCE, payload PHP dopo
        // l'header dell'immagine.
        $polyglot = UploadedFile::fake()->createWithContent(
            'avatar.php',
            "GIF89a\n<?php echo 'pwned'; ?>"
        );

        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'avatar' => $polyglot,
        ])->assertSessionHasErrors('avatar');

        $this->assertNull($this->studente->fresh()->avatar);
        Storage::disk('local')->assertDirectoryEmpty('avatars');
    }

    public function test_un_avatar_valido_non_finisce_nella_webroot_pubblica(): void
    {
        Storage::fake('local');

        $avatar = UploadedFile::fake()->image('avatar.png', 100, 100);

        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'avatar' => $avatar,
        ]);

        $filename = $this->studente->fresh()->avatar;

        $this->assertNotNull($filename);
        $this->assertFileDoesNotExist(public_path('avatars/'.$filename));
        Storage::disk('local')->assertExists('avatars/'.$filename);
    }

    public function test_lestensione_salvata_ignora_il_nome_dichiarato_dal_client(): void
    {
        Storage::fake('local');

        // La validazione blocca gia' da sola un client che dichiara 'evil.php'
        // (shouldBlockPhpUpload in Laravel >=10.x), ma non blocca un'estensione
        // qualunque che non sia nella sua lista: qui il client chiama il file
        // 'avatar.bak' pur dichiarando un contenuto image/png. Prima della
        // correzione l'estensione salvata su disco veniva presa proprio da
        // getClientOriginalExtension(), cioe' da questa stringa.
        $avatar = UploadedFile::fake()->create('avatar.bak', 10, 'image/png');

        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'avatar' => $avatar,
        ]);

        $filename = $this->studente->fresh()->avatar;

        $this->assertNotNull($filename);
        $this->assertStringEndsWith('.png', $filename);
    }

    public function test_lavatar_non_e_raggiungibile_senza_autenticazione(): void
    {
        $this->get(route('user.avatar', 'qualunque.png'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------
    // 9. Hardening di produzione (Step 2): rotte, validazioni, header
    // -----------------------------------------------------------------

    public function test_la_correzione_non_e_piu_raggiungibile_via_get(): void
    {
        // La versione GET permetteva di rivalutare un esame aprendo un
        // semplice link, bypassando il controllo CSRF. La rotta POST allo
        // stesso URI resta registrata, quindi una GET e' un metodo non
        // consentito (405), non un percorso inesistente (404).
        $this->actingAs($this->docente)->get('/examcorrect')->assertStatus(405);
    }

    public function test_la_correzione_rifiuta_un_esame_inesistente(): void
    {
        $this->actingAs($this->docente)->post(route('display.users.answerP'), [
            'exam_id' => 999999,
            'user_id' => $this->studente->id,
        ])->assertSessionHasErrors('exam_id');
    }

    public function test_le_rotte_di_stampa_e_risultati_restituiscono_404_su_un_id_inesistente(): void
    {
        $this->actingAs($this->docente)->get(route('print.blankexam', 999999))->assertNotFound();
        $this->actingAs($this->docente)->get(route('show.users.results.index', 999999))->assertNotFound();
        $this->actingAs($this->docente)->get(route('print.exam', ['idexam' => 999999, 'iduser' => 999999]))->assertNotFound();
    }

    public function test_il_profilo_rifiuta_unemail_non_valida(): void
    {
        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => 'non-una-email',
        ])->assertSessionHasErrors('email');
    }

    public function test_il_profilo_rifiuta_unemail_gia_in_uso(): void
    {
        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->altroStudente->email,
        ])->assertSessionHasErrors('email');
    }

    public function test_cambiare_password_richiede_la_password_attuale(): void
    {
        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'password' => 'nuovapassword123',
            'confirm_password' => 'nuovapassword123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_cambiare_password_con_quella_attuale_corretta_funziona(): void
    {
        // UserFactory hasha 'password' per ogni utente creato senza un
        // valore esplicito: e' la password in chiaro nota qui.
        $this->actingAs($this->studente)->post(route('user.profile.store'), [
            'name' => $this->studente->name,
            'email' => $this->studente->email,
            'current_password' => 'password',
            'password' => 'nuovapassword123',
            'confirm_password' => 'nuovapassword123',
        ])->assertSessionDoesntHaveErrors();

        $this->assertTrue(Hash::check('nuovapassword123', $this->studente->fresh()->password));
    }

    public function test_la_registrazione_rifiuta_una_password_troppo_corta(): void
    {
        $this->post(route('register.custom'), [
            'name' => 'Tizio',
            'email' => 'corta@mexam.test',
            'password' => 'short1',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'corta@mexam.test']);
    }

    public function test_le_risposte_includono_gli_header_di_sicurezza(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertTrue(
            $response->headers->has('Content-Security-Policy-Report-Only'),
            'Manca la Content-Security-Policy-Report-Only.'
        );
    }
}
