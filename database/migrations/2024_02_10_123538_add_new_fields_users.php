<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * avatar/phone/city sono gia' create da create_users_table: questa
     * migration non ha piu' nulla da fare in avanti.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

        });
    }

    /**
     * up() sopra non crea nulla, quindi non c'e' nulla da disfare: prima
     * invece droppava avatar/phone/city, colonne che questa migration non ha
     * mai creato. Un migrate:rollback su una migrate:fresh pulita le
     * cancellava comunque, perche' down() non rispecchiava up().
     */
    public function down(): void
    {
        //
    }
};
