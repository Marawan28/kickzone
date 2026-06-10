<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TeamRequestAccepted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Team $team
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'message' => 'Team request accepted.',
        ];
    }
}

