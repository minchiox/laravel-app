<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    /** 'id' era mass assignable (la chiave primaria): stessa classe di falla
     * gia' chiusa su Exam/User/Library.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'user_id',
        'answer',
        'answer_text',
        'points',
        'exam_id',
    ];

    /**
     * Senza cast la colonna arriva dal DB come e' rappresentata dal driver
     * (stringa o intero secondo i casi): il confronto con Quiz::$answer, gia'
     * castato a boolean, non era affidabile.
     */
    protected $casts = [
        'answer' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
