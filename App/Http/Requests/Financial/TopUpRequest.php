<?php



// FILE: app/Http/Requests/Financial/TopUpRequest.php
// ============================================================
namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'    => ['required', 'numeric', 'min:10'],
            'method'    => ['required', 'in:vodafone_cash,visa,instapay'],
            'reference' => ['required', 'string'],
        ];
    }
}

// ============================================================