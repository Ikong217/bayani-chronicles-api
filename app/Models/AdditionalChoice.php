<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalChoice extends Model
{
    use HasFactory;

    protected $table = 'additional_choices';
    protected $fillable = [
        'level_id',
        'choice',
    ];

    public function level(){
        return $this->belongsTo(Level::class);
    }
}
