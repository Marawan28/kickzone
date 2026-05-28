<?php


// FILE: app/Models/Booking.php
// ============================================================
namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'slot_id', 'status', 'payment_id','field_id',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function slot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FieldSlot::class, 'slot_id');
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getQrCodeAttribute(): string
    {
        return 'KZ-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

  
}

// ============================================================