<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\PatchPostRequest;
use App\Http\Requests\PutPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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

    public function store(StorePostRequest $request): JsonResponse
    {
        $category = $this->takeCategoryId($request);

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

    public function updatePut(Post $post, PutPostRequest $request): JsonResponse
    {
        $category = $this->takeCategoryId($request);

        try {
            $post->update([
                'title' => $request->str('title'),
                'body' => $request->input('content'),
                'state' => $request->enum('state', PostStatus::class),
                'category_id' => $category->id,
            ]);

            return response()->json(['success' => 'Пост успешно обновлён'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка обновления поста: '.$e->getMessage()], 500);
        }

    }

    public function updatePatch(Post $post, PatchPostRequest $request): JsonResponse
    {
        $category = $this->takeCategoryId($request);

        // TODO: использовать DTO

        try {
            $data = [];

            if ($request->has('title')) {
                $data['title'] = $request->str('title');
            }

            if ($request->has('content')) {
                $data['body'] = $request->input('content');
            }

            if ($request->has('state')) {
                $data['state'] = $request->enum('state', PostStatus::class);
            }

            if (! empty($category)) {
                $data['category_id'] = $category->id;
            }

            if (! empty($data)) {
                $post->update($data);
            }

            return response()->json(['success' => 'Пост успешно обновлён'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка обновления поста: '.$e->getMessage()], 500);
        }

    }

    public function delete(Post $post, PatchPostRequest $request): JsonResponse
    {
        $post->delete();

        return response()->json(['success' => 'Пост успешно удален'], 200);
    }

    protected function takeCategoryId(ApiRequest $request)
    {
        return Category::query()->where('name', $request->str('category_name'))->first();
    }
}
