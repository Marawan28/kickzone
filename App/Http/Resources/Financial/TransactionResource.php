<?php


// FILE: app/Http/Resources/Financial/TransactionResource.php
// ============================================================
namespace App\Http\Resources\Financial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'amount'     => $this->amount,
            'type'       => $this->type,
            'status'     => $this->status,
       'created_at' => $this->created_at ? \Carbon\Carbon::parse($this->created_at)->toISOString() : null,
        ];
    }
}
