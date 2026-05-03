<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Http/Middleware/EnsureUserIsPlayer.php
// ============================================================
namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict routes to players only.
 */
class EnsureUserIsPlayer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== UserRole::Player) {
            return response()->json([
                'message' => 'This action is restricted to players.',
            ], 403);
        }

        return $next($request);
    }
}
