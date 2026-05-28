<?php


// FILE: app/Http/Requests/Booking/CreateBookingRequest.php
// ============================================================
namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'field_id'     => 'required|exists:fields,id',
            'slot_id'        => ['required', 'integer', 'exists:field_slots,id'],
            'payment_method' => ['required', 'string'],
            'discount_code'  => ['nullable', 'string'],
        ];
    }
}

// ============================================================