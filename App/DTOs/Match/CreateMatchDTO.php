<?php



// FILE: app/DTOs/Match/CreateMatchDTO.php
// ============================================================
namespace App\DTOs\Match;

final class CreateMatchDTO
{
    public function __construct(
        public readonly int    $creatorId,
        public readonly int    $fieldId,
        public readonly string $matchDate,
        public readonly int    $maxPlayers,
    ) {}

    public static function fromArray(array $data, int $creatorId): self
    {
        return new self(
            creatorId:  $creatorId,
            fieldId:    (int) $data['field_id'],
            matchDate:  $data['match_date'],
            maxPlayers: (int) $data['max_players'],
        );
    }
}

// ============================================================