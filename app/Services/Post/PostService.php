<?php

namespace App\Services\Post;

use App\Enums\PostStatus;
use App\Http\Requests\Post\ApiRequest;
use App\Http\Requests\Post\PutPostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostService
{
    protected Post $post;

    public function setPost(Post $post): PostService
    {
        $this->post = $post;

        return $this;
    }

    public function getPublished(array $fields = ['id', 'title', 'thumbnail', 'views', 'created_at']): Collection
    {

        return Post::query()->select($fields)->whereStatus(PostStatus::Published)->get();
    }

    public function addPost($request): JsonResponse
    {
        try {
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

            return responseSuccess('Пост успешно добавлен');
        } catch (\Exception $e) {
            return responseFail($e, 'Ошибка добавления поста', 500);
        }
    }

    public function putValidationToUpdatePost($updateRequest): JsonResponse
    {
        $putRequest = new PutPostRequest();
        $putRequest->merge($updateRequest->all());
        $validator = Validator::make($putRequest->all(), $putRequest->rules());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return $this->handlePutUpdate($putRequest);
    }

    private function handlePutUpdate(PutPostRequest $request): JsonResponse
    {
        //TODO:: Проверить типизацию класса валидации для $request

        $category = $this->takeCategoryId($request);

        try {
            $this->post->update([
                'title' => $request->str('title'),
                'body' => $request->input('content'),
                'state' => $request->enum('state', PostStatus::class),
                'category_id' => $category->id,
            ]);

            return responseSuccess('Пост успешно обновлён');
        } catch (\Exception $e) {
            return responseFail($e, 'Ошибка обновления поста', 500);
        }
    }

    public function PatchUpdatePost(UpdatePostRequest $request): JsonResponse
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
                $category = $this->takeCategoryId($request);
                $data['category_id'] = $category->id;
            }

            if (! empty($data)) {
                $this->post->update($data);
            } else {
                return response()->json(['No Content' => 'Нет данных для обновления'], 204);
            }

            return responseSuccess('Пост успешно обновлён');
        } catch (\Exception $e) {
            return responseFail($e, 'Ошибка обновления поста', 500);
        }
    }

    public function comment($request): Comment
    {

        /**
         * @var $comment Comment
         */
        $comment = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
            'text' => $request->str('text'),
        ]);

        return $comment;
    }

    public function takeCategoryId(ApiRequest $request): Category
    {
        return Category::query()->where('name', $request->str('category_name'))->first();
    }
}
