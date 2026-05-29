<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Events/MatchmakingMatchedEvent.php
// ============================================================
namespace App\Events;

use App\Models\MatchGame;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MatchmakingMatchedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly MatchGame  $match,
        public readonly Collection $matchedEntries,
    ) {}

    /**
     * Broadcast to each matched user's private channel.
     * For team entries, broadcast to all team members.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        foreach ($this->matchedEntries as $entry) {
            if ($entry->matchable_type === \App\Models\User::class) {
                // Solo player → broadcast to their private channel
                $channels[] = new PrivateChannel('user.' . $entry->matchable_id);
            } elseif ($entry->matchable_type === \App\Models\Team::class) {
                // Team → broadcast to each team member's private channel
                $team = \App\Models\Team::with('members')->find($entry->matchable_id);
                if ($team) {
                    foreach ($team->members as $member) {
                        $channels[] = new PrivateChannel('user.' . $member->user_id);
                    }
                }
            }
        }

        return $channels;
    }

    /**
     * Data sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type'     => 'matchmaking_matched',
            'match_id' => $this->match->id,
            'message'  => 'تم العثور على ماتش ليك! ⚽🎉',
        ];
    }

    public function broadcastAs(): string
    {
        return 'matchmaking.matched';
    }
}
