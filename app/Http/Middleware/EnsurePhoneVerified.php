<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Http/Middleware/EnsurePhoneVerified.php
// ============================================================
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block users who haven't verified their phone number yet.
 */
class EnsurePhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->phone_verified_at) {
            return response()->json([
                'message' => 'Please verify your phone number before continuing.',
                'action'  => 'verify_phone',
            ], 403);
        }

        return $next($request);
    }
}
