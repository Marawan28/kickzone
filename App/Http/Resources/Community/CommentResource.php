<?php




// FILE: app/Http/Resources/Community/CommentResource.php
// ============================================================
namespace App\Http\Resources\Community;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'content'    => $this->content,
            'created_at' => $this->created_at?->diffForHumans(),
            'user'       => $this->whenLoaded('user', fn() => [
                'id'         => $this->user->id,
                'name'       => $this->user->name,
                'avatar_url' => $this->user->getFirstMediaUrl('avatar'),
            ]),
        ];
    }
}

// ============================================================