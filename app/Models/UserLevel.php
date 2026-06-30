<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    use HasFactory;

    protected $table = 'user_levels';

    protected $fillable = [
        'user_id',
        'game_level_id',
        'score',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Define constants with values
    const STATUS_ONGOING = 'Ongoing';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_RESTART = 'Restart';
    const STATUS_ABANDONED = 'Abandoned';
    const STATUS_FAILED = 'Failed';
    const STATUS_QUIT = 'Quit';

    const STATUS_OPTIONS = [
        self::STATUS_ONGOING,
        self::STATUS_COMPLETED,
        self::STATUS_RESTART,
        self::STATUS_ABANDONED,
        self::STATUS_FAILED,
        self::STATUS_QUIT,
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function GameLevel()
    {
        return $this->belongsTo(GameLevel::class);
    }
}
