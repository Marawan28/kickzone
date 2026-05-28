<?php


// FILE: app/Models/MatchPlayer.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    protected $table = 'match_players';
    protected $fillable = ['match_id', 'user_id'];

    public function match(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// ============================================================