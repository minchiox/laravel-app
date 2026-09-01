<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->string('answer_text')->nullable();
            $table->boolean('answer')->nullable();
            $table->integer('points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Il nome era al singolare ('user_answer'): dropIfExists non trovava
     * nulla da fare e restituiva successo, ma la tabella reale (plurale,
     * come in up()) restava con la sua foreign key verso quizzes, bloccando
     * il rollback di create_quizzes_table.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
