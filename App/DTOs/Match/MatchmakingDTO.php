<?php




// FILE: app/DTOs/Match/MatchmakingDTO.php
// ============================================================
namespace App\DTOs\Match;

final class MatchmakingDTO
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $position,
        public readonly int    $cityId,
        public readonly string $timeSlot,       // morning|evening|night
        public readonly ?string $customTime,
        public readonly int    $minBudget,
        public readonly int    $maxBudget,
        public readonly string $mode,           // solo|team
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            userId:     $userId,
            position:   $data['position'],
            cityId:     (int) $data['city_id'],
            timeSlot:   $data['time_slot'],
            customTime: $data['custom_time'] ?? null,
            minBudget:  (int) $data['min_budget'],
            maxBudget:  (int) $data['max_budget'],
            mode:       $data['mode'],
        );
    }
}

// ============================================================