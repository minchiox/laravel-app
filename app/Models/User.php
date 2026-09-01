<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * `isTeacher` e' volutamente escluso: era mass assignable e
     * ProfileController salvava $request->all(), quindi bastava aggiungere
     * isTeacher=1 al form del profilo per promuoversi docente.
     * Il ruolo si assegna dal comando `php artisan mexam:make-teacher`.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'city',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array

     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array

     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // senza cast il valore arriva dal DB come 0/1 e per gli studenti
        // registrati prima della colonna e' null
        'isTeacher' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsToMany(Exam::class);
    }
}
