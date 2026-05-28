<?php


// FILE: app/Models/TeamRequest.php
// ============================================================
namespace App\Models;

use App\Enums\TeamRequestStatus;
use Illuminate\Database\Eloquent\Model;

class TeamRequest extends Model
{
    protected $fillable = ['team_id', 'user_id', 'status'];

    protected $casts = [
        'status' => TeamRequestStatus::class,
    ];

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
