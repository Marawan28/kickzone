<?php


// FILE: app/Models/Match.php 
// ============================================================
namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'creator_id', 'field_id', 'match_date',
        'max_players', 'status',
    ];

    protected $casts = [
        'status'     => MatchStatus::class,
        'match_date' => 'datetime',
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function players(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'match_players')
                    ->withTimestamps();
    }

    public function chat(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Chat::class, 'match_id');
    }

    public function teams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Team::class, 'match_id');
    }

    public function isFull(): bool
    {
        return $this->players()->count() >= $this->max_players;
    }

    public function getAverageDsrAttribute(): float
    {
        return round(
            $this->players()->avg('dsr_score') ?? 0,
            1
        );
    }
}

// ============================================================