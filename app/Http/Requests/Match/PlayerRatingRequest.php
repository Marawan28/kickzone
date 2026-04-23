<?php



// FILE: app/Http/Requests/Match/PlayerRatingRequest.php
// ============================================================
namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class PlayerRatingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'rated_user_id' => ['required', 'integer', 'exists:users,id'],
            'speed'         => ['required', 'integer', 'min:1', 'max:5'],
            'passing'       => ['required', 'integer', 'min:1', 'max:5'],
            'shooting'      => ['required', 'integer', 'min:1', 'max:5'],
            'skill'         => ['required', 'integer', 'min:1', 'max:5'],
            'sportsmanship' => ['required', 'integer', 'min:1', 'max:5'],
            'badge'         => ['nullable', 'in:scorer,rock,rival'],
        ];
    }
}

// ============================================================