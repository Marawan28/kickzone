<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Collection;

class ChatService
{
    // جلب كل المحادثات اللي اليوزر مشترك فيها (عن طريق الماتشات بتاعته)
    public function getUserChats(int $userId): Collection
    {
        return Chat::whereHas('match.players', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->with('match:id')->get();
    }

    // جلب رسائل شات معين
    public function getChatMessages(int $chatId): Collection
    {
        return Message::where('chat_id', $chatId)
            ->with('user:id,name') // عشان نعرف مين اللي بعت الرسالة
            ->oldest() // ترتيب من الأقدم للأحدث
            ->get();
    }

   
    public function sendMessage(int $chatId, int $userId, string $message): Message
    {
      
    $message = Message::create([
        'chat_id' => $chatId,
        'sender_id' => $userId,
        'message' => $message
    ]);

    $message->load('user');

    $chat = Chat::with('match.players')->find($chatId);
    
    if ($chat && $chat->match) {
        $recipients = $chat->match->players->where('id', '!=', $userId);

     
        foreach ($recipients as $recipient) {
            $recipient->notify(new NewMessageNotification($message));
        }
    }
    return $message;
}
}