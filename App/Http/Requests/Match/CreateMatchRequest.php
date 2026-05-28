<?php




// FILE: app/Http/Requests/Match/CreateMatchRequest.php
// ============================================================
namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class CreateMatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'field_id'    => ['required', 'integer', 'exists:fields,id'],
            'match_date'  => ['required', 'date', 'after:now'],
            'max_players' => ['required', 'integer', 'min:2', 'max:22'],
        ];
    }
}

// ============================================================