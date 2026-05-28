<?php



// FILE: app/Http/Requests/Match/MatchmakingRequest.php
// ============================================================
namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class MatchmakingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'position'    => ['required', 'in:goalkeeper,defender,midfielder,attacker'],
            'city_id'     => ['required', 'integer', 'exists:cities,id'],
            'time_slot'   => ['required', 'in:morning,evening,night'],
            'custom_time' => ['nullable', 'string'],
            'min_budget'  => ['required', 'integer', 'min:0'],
            'max_budget'  => ['required', 'integer', 'gt:min_budget'],
            'mode'        => ['required', 'in:solo,team'],
        ];
    }
}

// ============================================================