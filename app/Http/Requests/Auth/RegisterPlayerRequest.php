<?php




// FILE: app/Http/Requests/Auth/RegisterPlayerRequest.php
// ============================================================
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="RegisterPlayerRequest", required={"name","phone","password","city_id"})
 */
class RegisterPlayerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
       return [
    'name'     => 'required|string',
    'phone'    => 'required|string|unique:users,phone',
    'password' => 'required|string|min:8|confirmed',
    'city_id'  => 'required',
    'email'    => 'nullable|email',
    'bio'      => 'nullable|string',
    'preferred_position' => 'nullable', // سيبها مفتوحة كدة للتجربة
    'terms'    => 'accepted',
];
}
};

// ============================================================