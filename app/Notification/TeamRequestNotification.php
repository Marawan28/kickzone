<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Notifications/TeamRequestNotification.php
// ============================================================
namespace App\Notifications;

use App\Models\{Team, User};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Team   $team,
        private readonly User   $requester,
        private readonly string $type, // 'request_received' | 'request_accepted' | 'request_rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $messages = [
            'request_received' => [
                'title' => 'New Team Join Request 👥',
                'body'  => $this->requester->name . ' wants to join your team "'
                         . $this->team->name . '"',
            ],
            'request_accepted' => [
                'title' => 'Team Request Accepted! 🎉',
                'body'  => 'You have been accepted to team "' . $this->team->name . '"',
            ],
            'request_rejected' => [
                'title' => 'Team Request Update',
                'body'  => 'Your request to join "' . $this->team->name . '" was not accepted.',
            ],
        ];

        return array_merge(
            $messages[$this->type],
            [
                'type'    => $this->type,
                'team_id' => $this->team->id,
            ]
        );
    }
}
