<?php


// FILE: app/Models/Payment.php
// ============================================================
namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'status',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Booking::class);
    }
}

// ============================================================