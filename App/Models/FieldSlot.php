<?php

// FILE: app/Models/FieldSlot.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldSlot extends Model
{
      public $timestamps = false;
    protected $fillable = [
        'field_id', 'start_time', 'end_time', 'is_booked',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'is_booked'  => 'boolean',
    ];

    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function booking(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Booking::class, 'slot_id');
    }

  
}

// ============================================================