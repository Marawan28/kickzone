<?php

namespace App\DTOs\Team;

class TeamRequestDTO
{
    public function __construct(
        public int $teamId,
        public int $userId,
        public ?string $status = 'pending'
    ) {}

    public static function fromRequest(int $teamId, int $userId): self
    {
        return new self(
            teamId: $teamId,
            userId: $userId
        );
    }
}