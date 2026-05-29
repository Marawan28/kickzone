<?php



// FILE: app/Http/Resources/Profile/ProfileResource.php
// ============================================================
namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'phone'              => $this->phone,
            'email'              => $this->email,
            'role'               => $this->role,
            'preferred_position' => $this->when(!$this->isOwner(), $this->preferred_position),
            'dsr_score'          => $this->when(!$this->isOwner(), $this->dsr_score),
            'balance'            => $this->balance,
            'bio'                => $this->bio,
            'city'               => $this->whenLoaded('city', fn() => [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ]),
            'fields_count'       => $this->when($this->isOwner(), $this->fields_count ?? null),
            'avatar_url'         => $this->getFirstMediaUrl('avatar'),
            'created_at'         => $this->created_at?->toISOString(),
        ];
    }
}

// ============================================================