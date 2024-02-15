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

    public function library()
    {
        return $this->belongsToMany(Library::class); //questo ritorna read id on null
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
