<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * L'ordine e' vincolante: le librerie agganciano quiz gia' esistenti e gli
     * esami agganciano quiz e utenti.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            QuizzesSeeder::class,
            LibrarySeeder::class,
            ExamSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Credenziali di demo (password: '.UserSeeder::PASSWORD.')');
        $this->command?->line('  docente  '.UserSeeder::TEACHER_EMAIL);
        $this->command?->line('  studente '.UserSeeder::STUDENT_EMAIL);
    }
}
