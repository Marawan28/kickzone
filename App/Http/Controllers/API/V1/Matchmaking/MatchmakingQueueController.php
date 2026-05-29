<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Http/Controllers/API/V1/Matchmaking/MatchmakingQueueController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Matchmaking;

use App\DTOs\Matchmaking\JoinQueueDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Matchmaking\JoinQueueRequest;
use App\Services\MatchmakingQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Matchmaking Queue", description="Queue-based matchmaking for solo players and teams")
 */
class MatchmakingQueueController extends Controller
{
    public function __construct(
        private readonly MatchmakingQueueService $matchmakingService,
    ) {}

    /**
     * Join the matchmaking queue.
     *
     * Solo players and teams can enter the queue. The system will
     * immediately attempt to find a compatible match. If no match
     * is found, the entry stays in the queue as 'waiting'.
     *
     * @OA\Post(path="/api/v1/matchmaking/queue", tags={"Matchmaking Queue"}, security={{"sanctum":{}}})
     */
    public function join(JoinQueueRequest $request): JsonResponse
    {
        try {
            $dto = JoinQueueDTO::fromRequest(
                $request->validated(),
                $request->user(),
            );

            $result = $this->matchmakingService->joinQueue($dto);

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
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $this->matchmakingService->cancelQueue($id, $request->user());

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
        $entry = $this->matchmakingService->getStatus($request->user());

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
