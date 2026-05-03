<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Http/Middleware/ForceJsonResponse.php
// ============================================================
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force all API responses to JSON, preventing HTML error pages.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}

// ============================================================
// Register in bootstrap/app.php (Laravel 11 style):
//
// ->withMiddleware(function (Middleware $middleware): void {
//     $middleware->alias([
//         'role'           => RoleMiddleware::class,
//         'owner'          => EnsureUserIsOwner::class,
//         'player'         => EnsureUserIsPlayer::class,
//         'phone.verified' => EnsurePhoneVerified::class,
//         'locale'         => SetApiLocale::class,
//     ]);
//     $middleware->prependToGroup('api', ForceJsonResponse::class);
//     $middleware->prependToGroup('api', SetApiLocale::class);
// })
