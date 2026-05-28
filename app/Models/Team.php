<?php

// FILE: app/Models/Team.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['match_id', 'name'];

    public function match(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamRequest::class);
    }

    public function getAverageDsrAttribute(): float
    {
        return round(
            $this->members()
                 ->join('users', 'team_members.user_id', '=', 'users.id')
                 ->avg('users.dsr_score') ?? 0,
            1
        );
    }

        public function players(): BelongsToMany
        {
            // 'team_user' هو الجدول الوسيط اللي بيربط التيم باليوزر
            return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id');
    }

    }