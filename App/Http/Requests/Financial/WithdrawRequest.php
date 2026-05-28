<?php



// FILE: app/Http/Requests/Financial/WithdrawRequest.php
// ============================================================
namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50'],
        ];
    }
}

// ============================================================