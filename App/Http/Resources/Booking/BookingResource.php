<?php



// FILE: app/Http/Resources/Booking/BookingResource.php
// ============================================================
namespace App\Http\Resources\Booking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'qr_code'    => $this->qr_code,
            'status'     => $this->status,
            'created_at' => \Carbon\Carbon::parse($this->booking_date)->toISOString(),
            'slot'       => $this->whenLoaded('slot', fn() => [
                'id'         => $this->slot->id,
                'start_time' => $this->slot->start_time?->format('Y-m-d H:i'),
                'end_time'   => $this->slot->end_time?->format('Y-m-d H:i'),
                'field'      => $this->slot->field ? [
                    'id'   => $this->slot->field->id,
                    'name' => $this->slot->field->name,
                    'city' => $this->slot->field->city?->name,
                ] : null,
            ]),
            'payment'    => $this->whenLoaded('payment', fn() => [
                'amount' => $this->payment->amount,
                'status' => $this->payment->status,
            ]),
        ];
    }
}

// ============================================================