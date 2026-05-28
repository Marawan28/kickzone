<?php


// FILE: app/Http/Requests/Profile/UpdateProfileRequest.php
// ============================================================
namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:100'],
            'phone'              => ['required', 'string'],
            'email'              => ['nullable', 'email'],
            'preferred_position' => ['required', 'in:goalkeeper,defender,midfielder,attacker'],
            'city_id'            => ['required', 'integer', 'exists:cities,id'],
            'bio'                => ['nullable', 'string', 'max:500'],
        ];
    }
}

// ============================================================