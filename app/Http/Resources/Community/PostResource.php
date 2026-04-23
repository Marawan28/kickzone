<?php




// FILE: app/Http/Resources/Community/PostResource.php
// ============================================================
namespace App\Http\Resources\Community;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'content'        => $this->content,
            'likes_count'    => $this->likes_count ?? $this->likes()->count(),
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'created_at' => $this->created_at ? \Carbon\Carbon::parse($this->created_at)->diffForHumans() : null,
            'image_url'      => $this->getFirstMediaUrl('post_images'),
            'user'           => $this->whenLoaded('user', fn() => [
                'id'         => $this->user->id,
                'name'       => $this->user->name,
                'avatar_url' => $this->user->getFirstMediaUrl('avatar'),
            ]),
            'city'           => $this->whenLoaded('city', fn() => [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ]),
        ];
    }
}

// ============================================================