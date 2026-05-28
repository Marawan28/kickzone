<?php



// FILE: app/Http/Requests/Community/AddCommentRequest.php
// ============================================================
namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:500'],
        ];
    }
}

// ============================================================