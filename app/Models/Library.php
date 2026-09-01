<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Library extends Model
{
    use HasFactory;

    /**
     * 'id' era mass assignable (la chiave primaria), come per Exam prima
     * dello Step 1: un POST su /library con id= poteva scontrarsi con una
     * riga esistente o imporre un id scelto dal chiamante.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'library_name',
        'library_subject',
        'library_difficulty',
    ];

    public function quiz()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
