<?php

// FILE: app/Http/Requests/Auth/RegisterOwnerRequest.php
// ============================================================
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterOwnerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

// ============================================================