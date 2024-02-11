<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Library extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'library_name',
        'library_subject',
        'library_difficulty',
    ];

    public function quiz()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
