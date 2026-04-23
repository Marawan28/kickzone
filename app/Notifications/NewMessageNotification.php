<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Message;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database']; // هنخزنها في الداتابيز عشان تظهر في الـ Notifications list
    }

    public function toArray($notifiable): array
    {
        return [
            'chat_id' => $this->message->chat_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->user->name,
            'message_preview' => substr($this->message->message, 0, 50), // أول 50 حرف بس
            'text' => "أرسل لك " . $this->message->user->name . " رسالة جديدة في الشات."
        ];
    }
}