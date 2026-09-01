<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array

     */
    protected $fillable = [
        'question',
        'answer',
        'answer_text',
        'subject',
        'difficulty',
        'points',
    ];

    /**
     * `answer` e `answer_text` sono le risposte corrette: non devono finire in
     * nessuna serializzazione JSON. L'endpoint /libraries/{id}/quizzes le
     * restituiva a qualunque utente autenticato, studenti compresi.
     *
     * Non influisce sull'accesso via proprieta' ($quiz->answer) usato dalle
     * view del docente e dalla correzione.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'answer',
        'answer_text',
    ];

    protected $casts = [
        'answer' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function library()
    {
        return $this->belongsToMany(Library::class);
    }

    public function exam()
    {
        return $this->belongsToMany(Exam::class);
    }
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

}
