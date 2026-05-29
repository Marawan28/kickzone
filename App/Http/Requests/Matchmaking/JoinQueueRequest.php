<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Http/Requests/Matchmaking/JoinQueueRequest.php
// ============================================================
namespace App\Http\Requests\Matchmaking;

use Illuminate\Foundation\Http\FormRequest;

class JoinQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:solo,team'],
            'team_id'        => ['required_if:type,team', 'integer', 'exists:teams,id'],
            'player_count'   => ['required', 'integer', 'in:6,10,14'],
            'preferred_time' => ['required', 'in:morning,evening,night'],
            'city_id'        => ['required', 'integer', 'exists:cities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'           => 'نوع البحث مطلوب (solo أو team).',
            'type.in'                 => 'نوع البحث لازم يكون solo أو team.',
            'team_id.required_if'     => 'لازم تحدد الفريق لما يكون النوع team.',
            'team_id.exists'          => 'الفريق المحدد غير موجود.',
            'player_count.required'   => 'عدد اللاعبين مطلوب.',
            'player_count.in'         => 'عدد اللاعبين لازم يكون 6 أو 10 أو 14.',
            'preferred_time.required' => 'الوقت المفضل مطلوب.',
            'preferred_time.in'       => 'الوقت لازم يكون morning أو evening أو night.',
            'city_id.required'        => 'المدينة مطلوبة.',
            'city_id.exists'          => 'المدينة المحددة غير موجودة.',
        ];
    }
}
