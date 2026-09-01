<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L'unicita' della partecipazione era affidata a un controllo applicativo
     * soggetto a race condition: due POST concorrenti producevano due consegne
     * per lo stesso studente. Qui la garantisce il database.
     */
    public function up(): void
    {
        Schema::table('exam_user', function (Blueprint $table) {
            $table->unique(['exam_id', 'user_id'], 'exam_user_partecipazione_unica');
        });
    }

    /**
     * exam_id non aveva un indice proprio: quello unico appena sopra e'
     * l'unico a coprirlo, e MySQL rifiuta di droppare un indice che sta
     * ancora supportando una foreign key. Va ricreato un indice singolo su
     * exam_id prima di rimuovere quello unico, nella stessa ALTER TABLE.
     */
    public function down(): void
    {
        Schema::table('exam_user', function (Blueprint $table) {
            $table->index('exam_id');
            $table->dropUnique('exam_user_partecipazione_unica');
        });
    }
};
