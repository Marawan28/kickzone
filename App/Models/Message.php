<?php


// FILE: app/Models/Message.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    const UPDATED_AT = null;
    protected $fillable = ['chat_id', 'sender_id', 'message', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function chat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chat::class);
     }

   /* public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
   {
        return $this->belongsTo(User::class, 'sender_id');
    }*/

    public function user():\Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // بنربط الرسالة باليوزر عن طريق عمود user_id
        return $this->belongsTo(User::class, 'sender_id');
    }
}

// ============================================================