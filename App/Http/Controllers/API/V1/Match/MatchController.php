<?php

// ============================================================
// FILE: app/Http/Controllers/API/V1/Match/MatchController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Match;

use App\DTOs\Match\{CreateMatchDTO, MatchmakingDTO, PlayerRatingDTO};
use App\DTOs\Matchmaking\JoinQueueDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\{CreateMatchRequest, MatchmakingRequest, PlayerRatingRequest};
use App\Http\Requests\Matchmaking\JoinQueueRequest;
use App\Http\Resources\Match\MatchResource;
use App\Services\MatchService;
use App\Services\MatchmakingQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Match", description="Match management & AI Matchmaking")
 */
class MatchController extends Controller
{
    public function __construct(
        public readonly MatchService $matchService,
        private readonly MatchmakingQueueService $matchmakingQueueService,
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

    /**
     * Join the matchmaking queue.
     *
     * Solo players and teams can enter the queue. The system will
     * immediately attempt to find a compatible match. If no match
     * is found, the entry stays in the queue as 'waiting'.
     *
     * @OA\Post(path="/api/v1/matchmaking/queue", tags={"Matchmaking Queue"}, security={{"sanctum":{}}})
     */
    public function joinQueue(JoinQueueRequest $request): JsonResponse
    {
        try {
            $dto = JoinQueueDTO::fromRequest(
                $request->validated(),
                $request->user(),
            );

            $result = $this->matchmakingQueueService->joinQueue($dto);

            $response = [
                'message' => $result['matched']
                    ? 'تم العثور على ماتش! ⚽🎉'
                    : 'تم إضافتك في طابور الانتظار... جاري البحث عن ماتش.',
                'entry'   => $result['entry'],
                'matched' => $result['matched'],
            ];

            if ($result['match']) {
                $response['match'] = $result['match'];
            }

            return response()->json($response, $result['matched'] ? 200 : 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel a matchmaking queue entry.
     *
     * Only the entry owner (or team member) can cancel.
     * Entries that are already matched cannot be cancelled.
     *
     * @OA\Delete(path="/api/v1/matchmaking/queue/{id}", tags={"Matchmaking Queue"}, security={{"sanctum":{}}})
     */
    public function cancelQueue(Request $request, int $id): JsonResponse
    {
        try {
            $this->matchmakingQueueService->cancelQueue($id, $request->user());

            return response()->json([
                'message' => 'تم إلغاء البحث عن ماتش.',
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get current matchmaking queue status for the authenticated user.
     *
     * Returns the active queue entry (waiting or recently matched).
     *
     * @OA\Get(path="/api/v1/matchmaking/queue/status", tags={"Matchmaking Queue"}, security={{"sanctum":{}}})
     */
    public function status(Request $request): JsonResponse
    {
        $entry = $this->matchmakingQueueService->getStatus($request->user());

        if (!$entry) {
            return response()->json([
                'message' => 'أنت مش في طابور الانتظار حالياً.',
                'entry'   => null,
            ]);
        }

        return response()->json([
            'message' => match ($entry->status->value) {
                'waiting' => 'جاري البحث عن ماتش...',
                'matched' => 'تم العثور على ماتش! ⚽',
                default   => $entry->status->value,
            },
            'entry' => $entry->load('match'),
        ]);
    }
}
