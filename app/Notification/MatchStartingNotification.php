<?php

declare(strict_types=1);



// ============================================================
// FILE: app/Notifications/MatchStartingNotification.php
// (1 hour before match — dispatched by a scheduled job)
// ============================================================
namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchStartingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MatchGame $match,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'     => 'match_starting',
            'title'    => 'Match Starting Soon! ⚽',
            'body'     => 'Your match at '
                        . $this->match->field->name
                        . ' starts in 1 hour. Get ready!',
            'match_id' => $this->match->id,
        ];
    }
}
