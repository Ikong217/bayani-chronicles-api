<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppliedSection extends Model
{
    use HasFactory;

    protected $table = 'applied_sections';

    protected $fillable = [
        'section_id',
        'user_id',
    ];

    public function Section(){
        return $this->belongsTo(Section::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }
}
