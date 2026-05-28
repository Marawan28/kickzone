<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Console/Commands/FinishExpiredMatches.php
// ============================================================
namespace App\Console\Commands;

use App\Enums\MatchStatus;
use App\Models\MatchGame;
use Illuminate\Console\Command;

/**
 * Runs hourly. Auto-finishes matches whose match_date has passed.
 */
class FinishExpiredMatches extends Command
{
    protected $signature   = 'laravel:finish-expired-matches';
    protected $description = 'Mark past matches as finished';

    public function handle(): void
    {
        $count = MatchGame::whereIn('status', [
                MatchStatus::Open->value,
                MatchStatus::Full->value,
            ])
            ->where('match_date', '<', now()->subHours(2))
            ->update(['status' => MatchStatus::Finished->value]);

        $this->info("Finished {$count} expired matches.");
    }
}
