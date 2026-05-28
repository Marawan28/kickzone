<?php


// FILE: app/Services/MatchService.php
// ============================================================
namespace App\Services;

use App\DTOs\Match\{CreateMatchDTO, MatchmakingDTO, PlayerRatingDTO};
use App\Enums\MatchStatus;
use App\Models\MatchGame;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchService
{
    public function __construct(
        public readonly MatchRepositoryInterface $matchRepo,
        public readonly DsrService              $dsrService,
    ) {}

    public function createMatch(CreateMatchDTO $dto): MatchGame
    {
        $match = $this->matchRepo->create([
            'creator_id'  => $dto->creatorId,
            'field_id'    => $dto->fieldId,
            'match_date'  => $dto->matchDate,
            'max_players' => $dto->maxPlayers,
            'status'      => MatchStatus::Open,
        ]);

        // Add creator as first player
        $this->matchRepo->addPlayer($match->id, $dto->creatorId);

        // Create a chat room for the match
        \App\Models\Chat::create(['match_id' => $match->id]);

        return $match->load(['field.city', 'players', 'creator']);
    }

    public function joinMatch(int $matchId, int $userId): MatchGame
    {
        $match = $this->matchRepo->findById($matchId);

        if (! $match) {
            throw new \DomainException('Match not found.');
        }

        if ($match->isFull()) {
            throw new \DomainException('Match is already full.');
        }

        if ($match->players()->where('user_id', $userId)->exists()) {
            throw new \DomainException('You are already in this match.');
        }

        $this->matchRepo->addPlayer($matchId, $userId);

        $match->refresh();

        if ($match->isFull()) {
            $this->matchRepo->updateStatus($matchId, MatchStatus::Full->value);
        }

        return $match->load(['field.city', 'players']);
    }

    public function submitRatings(PlayerRatingDTO $dto): void
    {
        $this->dsrService->processRating($dto);
    }

    /**
     * AI Matchmaking: finds open matches that best fit the player's
     * DSR level, position, city, time, and budget preferences.
     * Returns matches sorted by a computed compatibility score (%).
     */
    public function findMatchesForPlayer(MatchmakingDTO $dto): \Illuminate\Support\Collection
    {
        $user = \App\Models\User::find($dto->userId);

        $matches = $this->matchRepo->findOpenMatchesForMatchmaking([
            'city_id'   => $dto->cityId,
            'max_price' => $dto->maxBudget,
        ]);

        return $matches
            ->map(function (MatchGame $match) use ($user, $dto): array {
                $score = $this->computeMatchScore($match, $user, $dto);
                return ['match' => $match, 'score' => $score];
            })
            ->sortByDesc('score')
            ->values();
    }

    /**
     * DSR + position + time proximity scoring algorithm.
     * Returns a score between 0-100.
     */
    private function computeMatchScore(
        MatchGame $match,
        \App\Models\User $user,
        MatchmakingDTO $dto
    ): int {
        $score = 0;

        // DSR proximity (max 50 points)
        $avgDsr    = $match->average_dsr;
        $dsrDiff   = abs($user->dsr_score - $avgDsr);
        $dsrPoints = max(0, 50 - (int) ($dsrDiff / 2));
        $score    += $dsrPoints;

        // Position needed (max 30 points)
        $positionNeeded = $match->players()
            ->pluck('preferred_position')
            ->toArray();
        if (! in_array($user->preferred_position->value, $positionNeeded, true)) {
            $score += 30; // Player fills a needed position
        }

        // Budget match (max 20 points)
        $price = $match->field->price_per_hour;
        if ($price >= $dto->minBudget && $price <= $dto->maxBudget) {
            $score += 20;
        }

        return min(100, $score);
    }

    public function finishMatch(int $matchId): void
    {
        $this->matchRepo->updateStatus($matchId, MatchStatus::Finished->value);
    }
}

// ============================================================