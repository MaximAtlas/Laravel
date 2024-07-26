<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        // TODO: убрать, когда мы будем работать с авторизацией
        auth()->login(User::query()->where(['role' => 'admin'])->inRandomOrder()->first());
    }

    public function index(): array
    {
        return Post::query()->select('title', 'thumbnail', 'views', 'created_at')->get()->toArray();
    }

    public function show(Post $post): array
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

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $category = Category::query()->where('name', $request->str('category_name'))->first();

        /*if (!$category) {
            return response()->json([
                'error' => 'Категория не найдена',
            ], 404);
        }*/

        $path = $request->file('image')->storePublicly('images');

        /** @var Post $post */
        $post = auth()->user()->posts()->create([
            'title' => $request->str('title'),
            'body' => $request->str('content'),
            'thumbnail' => config('app.url').Storage::url($path),
            'status' => $request->enum('state', PostStatus::class),
            'category_id' => $category->id,
        ]);

        return response()->json([
            'id' => $post->id,
        ], 201);
    }

    public function comment(Post $post, Request $request): array
    {
        return $post->comments()->create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'text' => $request->str('text'),
        ])->only('id');
    }
}
