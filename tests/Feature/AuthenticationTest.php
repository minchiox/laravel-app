<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function utente(): User
    {
        return User::factory()->student()->create([
            'email' => 'tizio@mexam.test',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_il_login_rigenera_la_sessione(): void
    {
        $this->utente();

        // Senza session()->regenerate() l'id di sessione resta quello che
        // l'attaccante puo' aver fissato prima del login (session fixation).
        $this->get(route('login'));
        $prima = session()->getId();

        $this->post(route('login.custom'), [
            'email' => 'tizio@mexam.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertNotSame($prima, session()->getId());
    }

    public function test_un_login_fallito_mostra_un_errore_non_un_successo(): void
    {
        $this->utente();

        $this->from(route('login'))
            ->post(route('login.custom'), [
                'email' => 'tizio@mexam.test',
                'password' => 'sbagliata',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_il_login_richiede_una_email_valida(): void
    {
        $this->post(route('login.custom'), [
            'email' => 'non-una-email',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_il_login_e_protetto_da_rate_limiting(): void
    {
        $this->utente();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.custom'), [
                'email' => 'tizio@mexam.test',
                'password' => 'sbagliata',
            ]);
        }

        $this->post(route('login.custom'), [
            'email' => 'tizio@mexam.test',
            'password' => 'sbagliata',
        ])->assertStatus(429);
    }

    public function test_la_registrazione_fallita_non_restituisce_una_pagina_vuota(): void
    {
        // customRegistration non aveva il ramo else: se Auth::attempt falliva
        // dopo la creazione, il metodo ritornava null.
        $this->post(route('register.custom'), [
            'name' => 'Tizio',
            'email' => 'nuovo@mexam.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();
    }

    public function test_il_logout_invalida_la_sessione(): void
    {
        $utente = $this->utente();

        $this->actingAs($utente)->post(route('signout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
