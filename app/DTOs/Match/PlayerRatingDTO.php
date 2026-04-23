<?php



// FILE: app/DTOs/Match/PlayerRatingDTO.php
// ============================================================
namespace App\DTOs\Match;

final class PlayerRatingDTO
{
    public function __construct(
        public readonly int $matchId,
        public readonly int $raterId,
        public readonly int $ratedUserId,
        public readonly int $speed,
        public readonly int $passing,
        public readonly int $shooting,
        public readonly int $skill,
        public readonly int $sportsmanship,
        public readonly ?string $badge,
    ) {}

    public function getAverageRating(): float
    {
        return round(
            ($this->speed + $this->passing + $this->shooting
            + $this->skill + $this->sportsmanship) / 5,
            2
        );
    }

    public static function fromArray(array $data, int $matchId, int $raterId): self
    {
        return new self(
            matchId:       $matchId,
            raterId:       $raterId,
            ratedUserId:   (int) $data['rated_user_id'],
            speed:         (int) $data['speed'],
            passing:       (int) $data['passing'],
            shooting:      (int) $data['shooting'],
            skill:         (int) $data['skill'],
            sportsmanship: (int) $data['sportsmanship'],
            badge:         $data['badge'] ?? null,
        );
    }
}

// ============================================================