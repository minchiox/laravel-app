<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Il DatabaseSeeder non creava nessun utente: dopo `migrate:fresh --seed` non
 * esisteva alcun account con cui fare login, quindi la demo era inaccessibile.
 */
class UserSeeder extends Seeder
{
    public const PASSWORD = 'password';

    public const TEACHER_EMAIL = 'docente@mexam.test';

    public const STUDENT_EMAIL = 'studente@mexam.test';

    public function run(): void
    {
        $this->user(self::TEACHER_EMAIL, 'Prof. Elena Ricci', isTeacher: true);

        $this->user(self::STUDENT_EMAIL, 'Marco Bianchi', isTeacher: false);
        $this->user('giulia@mexam.test', 'Giulia Conti', isTeacher: false);
        $this->user('luca@mexam.test', 'Luca Ferrari', isTeacher: false);
    }

    /**
     * updateOrCreate per restare idempotente: il seeder puo' essere rilanciato
     * senza duplicare gli account ne' cambiare le credenziali di demo.
     */
    private function user(string $email, string $name, bool $isTeacher): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'isTeacher' => $isTeacher,
                'email_verified_at' => now(),
            ],
        );
    }
}
