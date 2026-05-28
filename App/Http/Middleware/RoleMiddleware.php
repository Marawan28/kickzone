<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Http/Middleware/RoleMiddleware.php
// ============================================================
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Flexible role middleware.
 * Usage: Route::middleware('role:owner,admin')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role?->value;

        if (! in_array($userRole, $roles, true)) {
            return response()->json([
                'message' => "Access denied. Required role(s): " . implode(', ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
