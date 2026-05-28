<?php
// ============================================================
// FILE: app/Http/Controllers/API/SwaggerController.php
// Base OpenAPI annotations — generates the full Swagger spec.
// Run: php artisan l5-swagger:generate
// View: /api/documentation
// ============================================================
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="KickZone API",
 *     version="1.0.0",
 *     description="KickZone — Sports Field Booking & Community Platform. Multi-tenant API for Players and Stadium Owners.",
 *     @OA\Contact(email="dev@kickzone.app"),
 *     @OA\License(name="MIT")
 * )
 *
 * @OA\Server(url="http://localhost/api/v1", description="Local Development")
 * @OA\Server(url="https://api.kickzone.app/v1", description="Production")
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Sanctum token: Bearer {token}"
 * )
 *
 * @OA\Tag(name="Auth",      description="Authentication: Register, Login, OTP, Google OAuth")
 * @OA\Tag(name="Profile",   description="Player profile, avatar, DSR stats")
 * @OA\Tag(name="Fields",    description="Stadium browsing (players) & management (owners)")
 * @OA\Tag(name="Booking",   description="Field booking — concurrency-safe engine")
 * @OA\Tag(name="Wallet",    description="Ledger-based wallet: top-up, withdraw, split-bill")
 * @OA\Tag(name="Match",     description="Match management & AI Matchmaking (DSR-based)")
 * @OA\Tag(name="Teams",     description="Team management & join requests")
 * @OA\Tag(name="Community", description="Posts, comments, likes, city forums")
 * @OA\Tag(name="Chat",      description="Match-scoped real-time messaging")
 *
 * @OA\Components(
 *     @OA\Response(
 *         response="ValidationError",
 *         description="Validation failed",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation failed."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response="Unauthenticated",
 *         description="Token missing or invalid",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated. Please log in.")
 *         )
 *     ),
 *     @OA\Response(
 *         response="DomainError",
 *         description="Business rule violation",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Slot is no longer available.")
 *         )
 *     )
 * )
 */
class SwaggerController extends Controller
{
    // This class exists only to host the base OpenAPI annotations.
    // It has no routes.
}
