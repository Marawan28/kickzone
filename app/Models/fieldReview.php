<?php


// FILE: app/Models/FieldReview.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldReview extends Model
{
    protected $fillable = [
        'field_id', 'player_id', 'rating', 'comment',
    ];

    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function player(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}

// ============================================================