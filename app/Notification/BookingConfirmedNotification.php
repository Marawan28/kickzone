<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Booking $booking,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database']; // extend with 'fcm' via a custom channel
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'booking_confirmed',
            'title'      => 'Booking Confirmed! 🎉',
            'body'       => 'Your booking at '
                          . $this->booking->slot->field->name
                          . ' is confirmed. QR: '
                          . $this->booking->qr_code,
            'booking_id' => $this->booking->id,
        ];
    }
}
