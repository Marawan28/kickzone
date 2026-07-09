<?php

// FILE: app/Http/Controllers/API/V1/Dashboard/DashboardController.php
// ============================================================
declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Dashboard", description="Owner stadium dashboard statistics")
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/owner/dashboard",
     *     tags={"Dashboard"},
     *     summary="Get owner dashboard statistics",
     *     description="Returns today's profits, upcoming bookings count, and the stadium's average rating for the authenticated owner.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="owner_name", type="string", example="Ahmed"),
     *                 @OA\Property(property="today_profits", type="number", format="float", example=1250.00),
     *                 @OA\Property(property="upcoming_bookings_count", type="integer", example=6),
     *                 @OA\Property(property="stadium_rating", type="number", format="float", example=4.7),
     *                 @OA\Property(property="rating_label", type="string", example="Excellent"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden – only owners can access this endpoint")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isOwner()) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. Only stadium owners can view the dashboard.',
            ], 403);
        }

        $stats = $this->dashboardService->getOwnerStats($user->id);

        return response()->json([
            'status' => true,
            'data'   => $stats,
        ]);
    }
}

// ============================================================
