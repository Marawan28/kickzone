<?php


// FILE: app/Repositories/Contracts/MatchRepositoryInterface.php
// ============================================================
namespace App\Repositories\Contracts;

use App\Models\MatchGame;
use Illuminate\Support\Collection;

interface MatchRepositoryInterface
{
    public function create(array $data): MatchGame;
    public function findById(int $id): ?MatchGame;
    public function addPlayer(int $matchId, int $userId): void;
    public function findOpenMatchesForMatchmaking(array $filters): Collection;
    public function updateStatus(int $matchId, string $status): void;
}

// ============================================================