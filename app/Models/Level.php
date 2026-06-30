<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = "levels";
    protected $fillable = [
        'game_level_id',
        'type',
        'question',
        'answer',
        'ans1',
        'ans2',
        'ans3',
        'rationalization',
    ];

    public function GameLevel(){
        return $this->belongsTo(GameLevel::class);
    }
}
