<?php




// FILE: app/Http/Requests/Community/CreatePostRequest.php
// ============================================================
namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    public $timestamps = false;
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'city_id'    => ['required', 'integer', 'exists:cities,id'],
            'content'    => ['required', 'string', 'max:1000'],
            'visibility' => ['nullable', 'in:public,city_only'],
            'image'      => ['nullable', 'image', 'max:5120'],
        ];
    }
}

// ============================================================