<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['exams', 'quizzes', 'libraries'];

    /**
     * Nessuna delle tre tabelle aveva un proprietario: qualunque docente
     * leggeva, modificava, cancellava e perfino rivalutava il materiale e i
     * compiti di qualunque altro docente cambiando l'id nell'URL.
     *
     * Nullable perche' i record gia' esistenti vanno assegnati a un docente
     * reale prima che il vincolo abbia senso, non perche' un record senza
     * proprietario sia un caso valido: e' l'applicazione (Policy + creazione
     * via auth()->id()) a garantire che non accada piu'.
     */
    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }

        // Nel dataset di demo esiste un solo docente: i record creati prima
        // di questa migration gli vengono assegnati. Se non esiste ancora
        // nessun docente (es. migrate senza seed), non c'e' nulla da
        // backfillare.
        $ownerId = User::where('isTeacher', true)->orderBy('id')->value('id');

        if ($ownerId !== null) {
            foreach (self::TABLES as $table) {
                DB::table($table)->whereNull('user_id')->update(['user_id' => $ownerId]);
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['user_id']);
                $blueprint->dropColumn('user_id');
            });
        }
    }
};
