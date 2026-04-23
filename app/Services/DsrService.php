<?php


// FILE: app/Services/DsrService.php
// Dynamic Skill Rating — ELO-inspired calculation
// ============================================================
namespace App\Services;

use App\DTOs\Match\PlayerRatingDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class DsrService
{
    private const DSR_WEIGHT = 0.15; // how much each rating session affects DSR

    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
    ) {}

    /**
     * Process a player rating submitted after a match.
     *
     * Algorithm:
     *  1. Calculate average score from 5 metrics (1-5 stars each).
     *  2. Normalize to a 0-100 scale.
     *  3. Apply weighted average to existing DSR.
     *     new_dsr = (old_dsr * (1 - weight)) + (normalized_score * weight)
     */
    public function processRating(PlayerRatingDTO $dto): void
    {
        $ratedUser = $this->userRepo->findById($dto->ratedUserId);

        $avgRating      = $dto->getAverageRating(); // 1.0 - 5.0
        $normalized     = ($avgRating / 5) * 100;   // 20 - 100
        $currentDsr     = (float) $ratedUser->dsr_score;
        $newDsr         = ($currentDsr * (1 - self::DSR_WEIGHT))
                        + ($normalized * self::DSR_WEIGHT);

        $this->userRepo->updateDsrScore($dto->ratedUserId, round($newDsr, 2));
    }

    public function getDsrLabel(float $dsr): string
    {
        return match (true) {
            $dsr < 40  => 'Beginner',
            $dsr < 65  => 'Intermediate',
            $dsr < 80  => 'Advanced',
            default    => 'Intermediate',
        };
    }
}

// ============================================================