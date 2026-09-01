<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

/**
 * Stessa falla di ExamPolicy, sui quiz: modificare o cancellare un quiz non
 * controllava chi lo avesse creato.
 */
class QuizPolicy
{
    public function view(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->user_id;
    }

    public function update(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->user_id;
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->user_id;
    }
}
