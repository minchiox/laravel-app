<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Il messaggio di conferma/errore era renderizzato da un blocco identico
 * ripetuto in 14 view; ora lo mostra una sola volta il layout condiviso
 * (auth/layouts.blade.php). Verifica che, rimosso il blocco locale, il
 * messaggio compaia comunque sulla pagina di destinazione.
 */
class FlashMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_il_messaggio_di_conferma_viene_mostrato_dal_layout_condiviso(): void
    {
        $docente = User::factory()->teacher()->create();
        $quiz = Quiz::factory()->create(['user_id' => $docente->id]);

        $response = $this->actingAs($docente)->delete(route('quiz.destroy', $quiz->id));

        $response->assertRedirect(route('quiz.list'));
        $this->followRedirects($response)->assertSee('Quiz deleted successfully.');
    }
}
