<?php

// ============================================================
// FILE: app/Http/Controllers/API/V1/Match/MatchController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Match;

use App\DTOs\Match\{CreateMatchDTO, MatchmakingDTO, PlayerRatingDTO};
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\{CreateMatchRequest, MatchmakingRequest, PlayerRatingRequest};
use App\Http\Resources\Match\MatchResource;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Match", description="Match management & AI Matchmaking")
 */
class MatchController extends Controller
{
    public function __construct(
    public readonly MatchService $matchService,
    ) {}

    /**
     * @OA\Post(path="/api/v1/matches", tags={"Match"}, security={{"sanctum":{}}})
     */
    public function store(CreateMatchRequest $request): JsonResponse
    {
        $match = $this->matchService->createMatch(
            CreateMatchDTO::fromArray($request->validated(), $request->user()->id)
        );
        return response()->json(['data' => new MatchResource($match)], 201);
    }

    /**
     * @OA\Get(path="/api/v1/matches/{id}", tags={"Match"}, security={{"sanctum":{}}})
     */
    public function show(int $id): JsonResponse
    {
        $match = $this->matchService->matchRepo->findById($id);
        return response()->json(['data' => new MatchResource($match)]);
    }

    /**
     * @OA\Post(path="/api/v1/matches/{id}/join", tags={"Match"}, security={{"sanctum":{}}})
     */
    public function join(Request $request, int $id): JsonResponse
    {
        $match = $this->matchService->joinMatch($id, $request->user()->id);
        return response()->json([
            'message' => 'You have joined the match!',
            'data'    => new MatchResource($match),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matchmaking",
     *     tags={"Match"},
     *     summary="AI-powered match recommendations",
     *     security={{"sanctum":{}}}
     * )
     */
    public function matchmaking(MatchmakingRequest $request): JsonResponse
    {
        $results = $this->matchService->findMatchesForPlayer(
            MatchmakingDTO::fromArray($request->validated(), $request->user()->id)
        );

        return response()->json([
            'message' => count($results) . ' Matches found for you',
            'data'    => $results->map(fn ($r) => [
                'score' => $r['score'],
                'match' => new MatchResource($r['match']),
            ]),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/rate",
     *     tags={"Match"},
     *     summary="Submit post-match player rating (affects DSR)",
     *     security={{"sanctum":{}}}
     * )
     */
    public function submitRating(PlayerRatingRequest $request, int $id): JsonResponse
    {
        $this->matchService->submitRatings(
            PlayerRatingDTO::fromArray($request->validated(), $id, $request->user()->id)
        );
        return response()->json(['message' => 'Rating submitted. DSR updated.']);
    }

    /**
     * @OA\Patch(path="/api/v1/matches/{id}/finish", tags={"Match"}, security={{"sanctum":{}}})
     */
    public function finish(int $id): JsonResponse
    {
        $this->matchService->finishMatch($id);
        return response()->json(['message' => 'Match marked as finished.']);
    }
}
