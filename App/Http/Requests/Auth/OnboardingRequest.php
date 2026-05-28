<?php

// FILE: app/Http/Requests/Auth/OnboardingRequest.php
// ============================================================
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nickname'           => ['required', 'string', 'max:50'],
            'preferred_position' => ['required', 'in:goalkeeper,defender,midfielder,attacker'],
            'skill_level'        => ['required', 'int'],
        ];
    }
}

// ============================================================