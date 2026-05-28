<?php


// FILE: app/Models/Chat.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['match_id'];

    public function match(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }
}

// ============================================================