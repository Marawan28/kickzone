<?php

// FILE: app/Http/Resources/Match/MatchResource.php
// ============================================================
namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'match_date'   => $this->match_date?->format('Y-m-d H:i'),
            'max_players'  => $this->max_players,
            'average_dsr'  => $this->average_dsr,
            'player_count' => $this->players()->count(),
            'creator'      => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'field'        => $this->whenLoaded('field', fn() => [
                'id'             => $this->field->id,
                'name'           => $this->field->name,
                'address'        => $this->field->address,
                'price_per_hour' => $this->field->price_per_hour,
                'average_rating' => $this->field->average_rating,
                'city'           => $this->field->city?->name,
                'cover_url'      => $this->field->getFirstMediaUrl('cover'),
            ]),
            'players'      => $this->whenLoaded('players', fn() =>
                $this->players->map(fn ($p) => [
                    'id'         => $p->id,
                    'name'       => $p->name,
                    'dsr_score'  => $p->dsr_score,
                    'position'   => $p->preferred_position,
                    'avatar_url' => $p->getFirstMediaUrl('avatar'),
                ])
            ),
        ];
    }
}

// ============================================================