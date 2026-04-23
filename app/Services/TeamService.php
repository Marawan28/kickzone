<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TeamRequest;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

class TeamService
{
    public function createTeam(array $data)
    {
        return Team::create($data);
    }

    public function sendJoinRequest(int $teamId, int $userId)
    {
        // التأكد إن مفيش طلب موجود فعلاً
        return TeamRequest::firstOrCreate([
            'team_id' => $teamId,
            'user_id' => $userId,
            'status'  => 'pending'
        ]);
    }

    public function handleResponse(int $requestId, string $status)
    {
        return DB::transaction(function () use ($requestId, $status) {
            $teamRequest = TeamRequest::findOrFail($requestId);
            $teamRequest->update(['status' => $status]);

            if ($status === 'accepted') {
             $matchId = $teamRequest->team->match_id;

  
             DB::table('match_players')->insert([
                'match_id' => $matchId,
                'user_id'  => $teamRequest->user_id,
                'team_id'  => $teamRequest->team_id,
                'created_at' => now(),
    ]);

                // 2. نسمع في علاقة الـ Many-to-Many (players)
                $teamRequest->team->players()->syncWithoutDetaching([$teamRequest->user_id]);
                $user = User::find($teamRequest->user_id);
                $user->notify(new TeamRequestAccepted($teamRequest->team));
        
            }

            return $teamRequest;
        });
    }
}