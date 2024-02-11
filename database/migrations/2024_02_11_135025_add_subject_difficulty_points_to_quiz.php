<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function(Blueprint $table) {
            $table->string('difficulty')->nullable();
            $table->string('subject')->nullable();
            $table->integer('points')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz', function (Blueprint $table) {
            //
        });
    }
};
