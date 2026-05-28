<?php

declare(strict_types=1);
// ============================================================
// FILE: app/Notifications/SplitBillRequestNotification.php
// ============================================================
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SplitBillRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $initiatorName,
        private readonly float  $amount,
        private readonly int    $transactionId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'split_bill',
            'title'          => 'Split Bill Request 💸',
            'body'           => $this->initiatorName
                              . ' is requesting '
                              . $this->amount
                              . ' EGP from you for the match.',
            'transaction_id' => $this->transactionId,
            'amount'         => $this->amount,
        ];
    }
}
