<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Comment
 */
class PostCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'username' => $this->user->name,
            'text' => $this->text,
        ];
    }
}
