<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Notifications/DsrUpdatedNotification.php
// ============================================================
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DsrUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly float $oldDsr,
        private readonly float $newDsr,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $diff      = round($this->newDsr - $this->oldDsr, 1);
        $direction = $diff >= 0 ? '▲' : '▼';
        $emoji     = $diff >= 0 ? '📈' : '📉';

        return [
            'type'    => 'dsr_updated',
            'title'   => "DSR Updated {$emoji}",
            'body'    => "Your skill rating changed by {$direction}{$diff}. "
                       . "New DSR: {$this->newDsr}",
            'old_dsr' => $this->oldDsr,
            'new_dsr' => $this->newDsr,
        ];
    }
}