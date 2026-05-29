<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Notifications/MatchmakingMatchedNotification.php
// ============================================================
namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchmakingMatchedNotification extends Notification implements ShouldQueue
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
            'type'     => 'matchmaking_matched',
            'title'    => 'تم العثور على ماتش! ⚽🎉',
            'body'     => 'الماتشميكنج لقى ليك ماتش في '
                        . ($this->match->field?->name ?? 'ملعب'),
            'match_id' => $this->match->id,
        ];
    }
}
