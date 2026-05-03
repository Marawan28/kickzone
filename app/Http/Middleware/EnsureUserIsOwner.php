<?php

// ============================================================
// FILE: app/Http/Middleware/EnsureUserIsOwner.php
// ============================================================
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict routes to stadium owners only.
 * Usage: Route::middleware('role:owner')
 */
class EnsureUserIsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== UserRole::Owner) {
            return response()->json([
                'message' => 'This action is restricted to stadium owners.',
            ], 403);
        }

        return $next($request);
    }
}