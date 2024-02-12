<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'exam_name',
        'exam_totalpoints',
        'exam_dueAt',
        'exam_startAt',
    ];

    public function quiz()
    {
        return $this->belongsToMany(Quiz::class, );
    }

}
