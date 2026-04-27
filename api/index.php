<?php

/**
 * KickZone — Vercel Entry Point
 *
 * Vercel only allows serverless function entry-points inside the /api directory.
 * This file simply forwards every incoming request to Laravel's standard
 * public/index.php so the framework handles routing normally.
 */

require __DIR__ . '/../public/index.php';