<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Message $message
    ) {}

    public function via(object $notifiable): array
    {
        // Ensure it works with the existing notifications table / database channel.
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'chat_id' => $this->message->chat_id,
            'sender_id' => $this->message->sender_id,
            'message' => $this->message->message,
        ];
    }
}

