<?php

declare(strict_types=1);

// ============================================================
// FILE: app/DTOs/Matchmaking/JoinQueueDTO.php
// ============================================================
namespace App\DTOs\Matchmaking;

final class JoinQueueDTO
{
    public function __construct(
        public readonly string $type,            // 'solo' | 'team'
        public readonly int    $matchableId,     // user_id (solo) or team_id (team)
        public readonly string $matchableType,   // App\Models\User or App\Models\Team
        public readonly int    $playerCount,     // Total players needed (6, 10, 14)
        public readonly float  $skillLevel,      // DSR snapshot
        public readonly string $preferredTime,   // morning|evening|night
        public readonly int    $cityId,
    ) {}

    /**
     * Build DTO from validated request data.
     * Resolves the matchable type and skill level automatically.
     */
    public static function fromRequest(array $data, \App\Models\User $user): self
    {
        $type = $data['type'];

        if ($type === 'team') {
            $team = \App\Models\Team::with('members')->findOrFail($data['team_id']);
            $matchableId   = $team->id;
            $matchableType = \App\Models\Team::class;
            // Use the team's average DSR as skill level
            $skillLevel = $team->average_dsr;
        } else {
            $matchableId   = $user->id;
            $matchableType = \App\Models\User::class;
            $skillLevel    = (float) $user->dsr_score;
        }

        return new self(
            type:          $type,
            matchableId:   $matchableId,
            matchableType: $matchableType,
            playerCount:   (int) $data['player_count'],
            skillLevel:    $skillLevel,
            preferredTime: $data['preferred_time'],
            cityId:        (int) $data['city_id'],
        );
    }
}
