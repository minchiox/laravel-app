<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Il ruolo docente non e' piu' autodichiarabile in registrazione: si assegna
 * da qui, che e' l'unico punto in cui `isTeacher` viene scritto in modo
 * deliberato (la colonna e' fuori da $fillable).
 */
class MakeTeacher extends Command
{
    protected $signature = 'mexam:make-teacher
                            {email : Email dell\'utente}
                            {--revoke : Toglie il ruolo docente invece di assegnarlo}';

    protected $description = 'Assegna (o revoca) il ruolo di docente a un utente';

    public function handle(): int
    {
        $email = $this->argument('email');
        $revoke = (bool) $this->option('revoke');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Nessun utente con email {$email}.");

            return self::FAILURE;
        }

        // Assegnazione diretta: isTeacher e' escluso dal mass assignment.
        $user->isTeacher = ! $revoke;
        $user->save();

        $this->info($revoke
            ? "{$user->name} <{$email}> non e' piu' un docente."
            : "{$user->name} <{$email}> e' ora un docente.");

        return self::SUCCESS;
    }
}
