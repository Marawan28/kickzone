<?php

// FILE: app/Http/Requests/Auth/LoginRequest.php
// ============================================================
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
            'role'     => ['required', 'in:player,owner'],
        ];
    }
}

// ============================================================