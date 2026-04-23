<?php

// FILE: app/Http/Requests/Field/CreateFieldRequest.php
// ============================================================
namespace App\Http\Requests\Field;

use Illuminate\Foundation\Http\FormRequest;

class CreateFieldRequest extends FormRequest
{
    public $timestamps = false;
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'address'        => ['required', 'string'],
            'price_per_hour' => ['required', 'integer', 'min:1'],
            'city_id'        => ['required', 'integer', 'exists:cities,id'],
            'description'    => ['nullable', 'string'],
            'images'         => ['nullable', 'array'],
            'images.*'       => ['image', 'max:5120'],
        ];
    }
}
