<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *@mixin Post
 */
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'views' => $this->views,
            'authorName' => $this->user->name,
            'createdAt' => $this->created_at,
            'categoryName' => $this->category->name,
            'comments' => PostCommentResource::collection($this->comments),
        ];
    }
}
