<?php

namespace App\Http\Controllers\API\V1\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Team\StoreTeamRequest;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    // إنشاء فريق جديد
    public function store(StoreTeamRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['owner_id'] = auth()->id() ?? 1; // بنسجل اللي كريت الفريق كصاحب ليه

        $team = $this->teamService->createTeam($data);
        return response()->json($team, 201);
    }

    // إرسال طلب انضمام
    public function request(int $id): JsonResponse
    {
        // $id هنا هو الـ team_id
        $this->teamService->sendJoinRequest($id, auth()->id() ?? 1);
        return response()->json(['message' => 'Join request sent successfully']);
    }

    // الرد على الطلب (قبول/رفض)
    public function respondToRequest(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:accepted,rejected']);
        
        $this->teamService->handleResponse($id, $request->status);
        return response()->json(['message' => 'Request ' . $request->status]);
    }
}