<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Models/MatchmakingEntry.php
// ============================================================
namespace App\Models;

use App\Enums\MatchmakingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MatchmakingEntry extends Model
{
    protected $table = 'matchmaking_queue';

    protected $fillable = [
        'matchable_type',
        'matchable_id',
        'type',
        'player_count',
        'skill_level',
        'preferred_time',
        'city_id',
        'status',
        'matched_match_id',
    ];

    protected $casts = [
        'status'      => MatchmakingStatus::class,
        'skill_level' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────

    /**
     * Polymorphic relation: resolves to User (solo) or Team (team).
     */
    public function matchable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The match created after successful matchmaking.
     */
    public function match(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'matched_match_id');
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // ── Query Scopes ───────────────────────────────────────

    /**
     * Only entries that are still waiting for a match.
     */
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', MatchmakingStatus::Waiting);
    }

    /**
     * Filter by matchmaking type (solo or team).
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Filter by city.
     */
    public function scopeInCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Filter by player count.
     */
    public function scopeForPlayerCount(Builder $query, int $count): Builder
    {
        return $query->where('player_count', $count);
    }

    /**
     * Filter by preferred time.
     */
    public function scopeAtTime(Builder $query, string $time): Builder
    {
        return $query->where('preferred_time', $time);
    }

    /**
     * Filter by skill level within a ± tolerance range.
     */
    public function scopeWithinSkillRange(Builder $query, float $skillLevel, float $tolerance = 10.0): Builder
    {
        return $query->whereBetween('skill_level', [
            $skillLevel - $tolerance,
            $skillLevel + $tolerance,
        ]);
    }
}
