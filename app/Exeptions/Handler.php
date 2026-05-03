<?php
// ============================================================
// FILE: app/Exceptions/Handler.php  (Laravel 11 — bootstrap/app.php style)
//
// Register in bootstrap/app.php:
// ->withExceptions(function (Exceptions $exceptions): void {
//     KickZoneExceptionHandler::register($exceptions);
// })
// ============================================================
declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KickZoneExceptionHandler
{
    public static function register(Exceptions $exceptions): void
    {
        // ── Validation errors (422) ────────────────────────
        $exceptions->render(function (ValidationException $e): JsonResponse {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        });

        // ── Model not found (404) ──────────────────────────
        $exceptions->render(function (ModelNotFoundException $e): JsonResponse {
            $model = class_basename($e->getModel());
            return response()->json([
                'message' => "{$model} not found.",
            ], 404);
        });

        // ── Route not found (404) ──────────────────────────
        $exceptions->render(function (NotFoundHttpException $e): JsonResponse {
            return response()->json([
                'message' => 'Endpoint not found.',
            ], 404);
        });

        // ── Unauthenticated (401) ──────────────────────────
        $exceptions->render(function (AuthenticationException $e): JsonResponse {
            return response()->json([
                'message' => 'Unauthenticated. Please log in.',
            ], 401);
        });

        // ── Business logic / domain errors (422) ──────────
        $exceptions->render(function (\DomainException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        // ── Role / access errors (403) ─────────────────────
        $exceptions->render(function (UnauthorizedException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        });

        // ── Catch-all (500) ────────────────────────────────
        $exceptions->render(function (\Throwable $e): JsonResponse {
            return response()->json([
                'message' => app()->isProduction()
                    ? 'An unexpected error occurred.'
                    : $e->getMessage(),
                'trace'   => app()->isProduction() ? null : $e->getTrace(),
            ], 500);
        });
    }
}
