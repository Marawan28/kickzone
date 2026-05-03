<?php

// ============================================================
// FILE: app/Console/Commands/SendMatchReminders.php
// ============================================================
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MatchGame;
use App\Notifications\MatchStartingNotification;
use Illuminate\Console\Command;

/**
 * Runs every 5 minutes via the scheduler.
 * Finds matches starting in the next 60 minutes and notifies all players.
 */
class SendMatchReminders extends Command
{
    protected $signature   = 'laravel:match-reminders';
    protected $description = 'Send push notifications to players whose match starts in ~1 hour';

    public function handle(): void
    {
        $matches = MatchGame::with(['players'])
            ->where('status', 'open')
            ->whereBetween('match_date', [
                now()->addMinutes(55),
                now()->addMinutes(65),
            ])
            ->get();

        foreach ($matches as $match) {
            foreach ($match->players as $player) {
                $player->notify(new MatchStartingNotification($match));
            }
        }

        $this->info("Sent reminders for {$matches->count()} upcoming matches.");
    }
}
