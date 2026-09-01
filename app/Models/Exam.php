<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    /**
     * 'id' era mass assignable (la chiave primaria) e 'totalpoints' non
     * corrisponde ad alcuna colonna: la colonna reale e' 'total_points', che
     * resta fuori dai fillable perche' e' calcolata dai quiz associati.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exam_name',
        'dueAt',
        'startAt',
    ];

    /**
     * Senza cast le date arrivavano come stringhe e i confronti con now()
     * funzionavano solo per il formato prodotto da MySQL.
     */
    protected $casts = [
        'startAt' => 'datetime',
        'dueAt' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsToMany(Quiz::class);
    }

    /** Il docente che ha creato l'esame. Non 'user()': quel nome e' gia' preso
     *  dalla relazione verso gli studenti che lo hanno svolto. */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class)->withPivot('user_points');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /** L'esame accetta consegne in questo momento. */
    public function isOpen(): bool
    {
        return now()->between($this->startAt, $this->dueAt);
    }

    /** Lo studente ha gia' consegnato: la partecipazione si registra alla consegna. */
    public function wasSubmittedBy(int $userId): bool
    {
        return $this->user()->where('user_id', $userId)->exists();
    }
}
