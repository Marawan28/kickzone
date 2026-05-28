<?php





// FILE: app/Repositories/Eloquent/MatchRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\MatchGame;
use App\Enums\MatchStatus;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Support\Collection;

class MatchRepository implements MatchRepositoryInterface
{
    public function create(array $data): MatchGame
    {
        return MatchGame::create($data);
    }

    public function findById(int $id): ?MatchGame
    {
        return MatchGame::with(['field.city', 'players', 'creator'])->find($id);
    }

    public function addPlayer(int $matchId, int $userId): void
    {
        MatchGame::findOrFail($matchId)->players()->syncWithoutDetaching([$userId]);
    }

    public function findOpenMatchesForMatchmaking(array $filters): Collection
    {
        return MatchGame::with(['field.city', 'players', 'creator'])
            ->where('status', MatchStatus::Open)
            ->whereHas('field', function ($q) use ($filters): void {
                $q->where('city_id', $filters['city_id']);
                if (isset($filters['max_price'])) {
                    $q->where('price_per_hour', '<=', $filters['max_price']);
                }
            })
            ->whereRaw('(SELECT COUNT(*) FROM match_players WHERE match_game_id = matches.id) < matches.max_players')
            ->get();
    }

    public function updateStatus(int $matchId, string $status): void
    {
        MatchGame::where('id', $matchId)->update(['status' => $status]);
    }
}

// ============================================================