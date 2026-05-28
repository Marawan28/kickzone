<?php


// FILE: app/Models/FieldImage.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldImage extends Model
{
    protected $fillable = ['field_id', 'image_path'];

    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}

// ============================================================