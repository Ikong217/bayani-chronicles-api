<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInGameData extends Model
{
    use HasFactory;

    protected $table = 'user_in_game_data';
    protected $fillable = [
        'user_id',
        'scrolls',
        'levels',
        'summative',
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }
}
