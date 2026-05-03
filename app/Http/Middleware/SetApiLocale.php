<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Http/Middleware/SetApiLocale.php
// ============================================================
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read X-Locale header and set the app locale accordingly.
 * Supports: en, ar
 */
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('X-Locale', 'en');

        if (in_array($locale, ['en', 'ar'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
