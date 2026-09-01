<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica che l'infrastruttura di test regga davvero: migrazioni e seeder
 * girano su SQLite in memoria e l'autenticazione funziona.
 *
 * Serve come rete di sicurezza per gli step successivi del refactoring: se una
 * migrazione o un seeder si rompe, se ne accorge questo test e non la demo.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_migrazioni_e_i_seeder_girano(): void
    {
        $this->seed();

        $this->assertSame(4, User::count());
        $this->assertSame(36, Quiz::count());
        $this->assertSame(3, Exam::count());
    }

    public function test_il_seeder_collega_i_quiz_agli_esami(): void
    {
        $this->seed();

        // Il seeder precedente creava esami vuoti: aprirli mostrava una pagina
        // senza domande.
        $exam = Exam::first();

        $this->assertGreaterThan(0, $exam->quiz()->count());
        $this->assertSame(
            $exam->quiz()->sum('points'),
            (int) $exam->total_points,
        );
    }

    public function test_le_credenziali_di_demo_funzionano(): void
    {
        $this->seed();

        $response = $this->post(route('login.custom'), [
            'email' => UserSeeder::TEACHER_EMAIL,
            'password' => UserSeeder::PASSWORD,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_la_dashboard_richiede_autenticazione(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_un_utente_autenticato_vede_la_dashboard(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', UserSeeder::TEACHER_EMAIL)->first())
            ->get(route('dashboard'))
            ->assertOk();
    }
}
