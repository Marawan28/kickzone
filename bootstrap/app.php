<?php
// ============================================================
// FILE: bootstrap/app.php  — Laravel 11 Application Bootstrap
// This replaces the old Kernel.php files.
// ============================================================
declare(strict_types=1);

use App\Exceptions\KickZoneExceptionHandler;
use App\Http\Middleware\{
    EnsurePhoneVerified,
    EnsureUserIsOwner,
    EnsureUserIsPlayer,
    ForceJsonResponse,
    RoleMiddleware,
    SetApiLocale
};
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api:         __DIR__ . '/../routes/api.php',
        commands:    __DIR__ . '/../routes/console.php',
        health:      '/up',
        apiPrefix:   'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ── Global API middleware ──────────────────────────
        $middleware->prependToGroup('api', ForceJsonResponse::class);
        $middleware->prependToGroup('api', SetApiLocale::class);

        // ── Named middleware aliases ───────────────────────
        $middleware->alias([
            'role'           => RoleMiddleware::class,
            'owner'          => EnsureUserIsOwner::class,
            'player'         => EnsureUserIsPlayer::class,
            'phone.verified' => EnsurePhoneVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        KickZoneExceptionHandler::register($exceptions);
    })
    ->create();

