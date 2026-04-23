<?php


// FILE: app/Http/Requests/Financial/SplitBillRequest.php
// ============================================================
namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class SplitBillRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'booking_id'      => ['required', 'integer', 'exists:bookings,id'],
            'recipient_ids'   => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
            'total_amount'    => ['required', 'numeric', 'min:1'],
        ];
    }
}

// ============================================================