<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::query()->select('title', 'thumbnail', 'views', 'created_at')->get()->toArray();
    }

    public function show(Post $post)
    {



    return [
        'title' => $post->title,
        'body' => $post->body,
        'views' => $post->views,
        'authorName' => $post->user->name,
        'createdAt' => $post->created_at,
        'categoryName' => $post->category->name,
        'comments' => $post->comments->map(fn (Comment $comment) => [
            'username' => $comment->user->name,
            'text' => $comment->text,
            ]),
        ];

    }
}

