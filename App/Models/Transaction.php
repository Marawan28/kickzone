<?php


// FILE: app/Models/Transaction.php
// ============================================================
namespace App\Models;

use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
      public $timestamps = false;
    protected $fillable = [
        'user_id', 'amount', 'type', 'status',
    ];

    protected $casts = [
        'type'   => TransactionType::class,
        'status' => TransactionStatus::class,
        'amount' => 'decimal:2',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// ============================================================