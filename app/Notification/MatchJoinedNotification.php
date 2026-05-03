<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Notifications/MatchJoinedNotification.php
// ============================================================
namespace App\Notifications;

use App\Models\{MatchGame, User};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchJoinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MatchGame $match,
        private readonly User      $joiner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'     => 'match_joined',
            'title'    => 'New Player Joined! ⚽',
            'body'     => $this->joiner->name
                        . ' joined your match at '
                        . $this->match->field->name,
            'match_id' => $this->match->id,
        ];
    }
}
