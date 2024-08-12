<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Requests\Post\ApiRequest;
use App\Http\Requests\Post\PutPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(): array
    {
        return Post::query()->select('title', 'thumbnail', 'views', 'created_at')->get()->toArray();
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {

        $category = $this->takeCategoryId($request);

        /** @var Post $post */
        $post = auth()->user()->posts()->create([
            'title' => $request->str('title'),
            'body' => $request->str('content'),
            //'thumbnail' => config('app.url').Storage::url($path),
            'status' => $request->enum('state', PostStatus::class),
            'category_id' => $category->id,
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->storePublicly('images');
            $post->update(['thumbnail' => config('app.url').Storage::url($path)]);
        }

        return response()->json([
            'id' => $post->id,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, UpdatePostRequest $updateRequest): JsonResponse
    {
        $category = $this->takeCategoryId($updateRequest);

        $BoolPutRequest = $updateRequest->isMethod('put') ?? false;

        if ($BoolPutRequest) {

            $putRequest = new PutPostRequest();
            $putRequest->merge($updateRequest->all());
            $validator = Validator::make($putRequest->all(), $putRequest->rules());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return $this->handlePutUpdate($post, $updateRequest, $category);
        } else {
            return $this->handlePatchUpdate($post, $updateRequest, $category);
        }

    }

    private function handlePutUpdate(Post $post, ApiRequest $request, Category $category): JsonResponse
    {
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

    private function handlePatchUpdate(Post $post, ApiRequest $request, ?Category $category): JsonResponse
    {
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
            } else {
                return response()->json(['No Content' => 'Нет данных для обновления'], 204);
            }

            return response()->json(['success' => 'Пост успешно обновлён'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка обновления поста: '.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        try {

            $post->delete();

            return response()->json(['success' => 'Пост успешно удален'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка удаления поста: '.$e->getMessage()], 406);
        }
    }

    public function comment(Post $post, Request $request): array
    {

        // TODO: сделать валидацию данных коментария

        return $post->comments()->create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'text' => $request->str('text'),
        ])->only('id');
    }

    protected function takeCategoryId(ApiRequest $request)
    {
        return Category::query()->where('name', $request->str('category_name'))->first();
    }
}
