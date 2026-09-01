<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

/**
 * Nessuna delle azioni riservate al docente (vedere i risultati, correggere,
 * stampare, gestire i quiz collegati, modificare, cancellare) controllava
 * chi avesse creato l'esame: un qualunque docente poteva farle tutte anche
 * sul lavoro di un collega, cambiando l'id nell'URL. Il middleware isTeacher
 * resta a monte per il ruolo; qui si controlla solo la proprieta'.
 */
class ExamPolicy
{
    public function view(User $user, Exam $exam): bool
    {
        return $user->id === $exam->user_id;
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->id === $exam->user_id;
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->id === $exam->user_id;
    }
}
