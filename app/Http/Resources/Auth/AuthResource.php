<?php



// FILE: app/Http/Resources/Auth/AuthResource.php
// ============================================================
declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'phone'              => $this->phone,
            'email'              => $this->email,
            'role'               => $this->role,
            'city'               => $this->whenLoaded('city', fn() => [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ]),
            'avatar_url'         => $this->getFirstMediaUrl('avatar'),
        ];
    }
}

// ============================================================