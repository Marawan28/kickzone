<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * REQUIRED for Vercel deployment.
 *
 * Vercel sits behind a load balancer that forwards requests to port 80.
 * Without trusting the proxy, Laravel will generate http:// URLs instead
 * of https://, breaking Sanctum tokens, redirects, and asset URLs.
 *
 * Register in bootstrap/app.php:
 *   $middleware->append(TrustProxies::class);
 */
class TrustProxies extends Middleware
{
    /**
     * Trust ALL proxies — correct for Vercel's serverless infrastructure.
     * Vercel's IPs are dynamic so we cannot whitelist specific addresses.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR    |
        Request::HEADER_X_FORWARDED_HOST   |
        Request::HEADER_X_FORWARDED_PORT   |
        Request::HEADER_X_FORWARDED_PROTO  |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
