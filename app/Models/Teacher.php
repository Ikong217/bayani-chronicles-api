<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use Notifiable;

    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'password',
        'email',
        'birthday',
        'contact',
        'profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
