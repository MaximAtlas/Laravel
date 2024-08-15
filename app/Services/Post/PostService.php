<?php

namespace App\Services\Post;

use App\Enums\PostStatus;
use App\Http\Requests\Post\ApiRequest;
use App\Http\Requests\Post\PutPostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Services\Post\DTO\CreatePostData;
use App\Services\Post\DTO\UpdatePostData;
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

    public function addPost(CreatePostData $data): JsonResponse
    {
        try {
            /** @var Post $post */
            $post = auth()->user()->posts()->create(
                $data->except('image')->toArray()
            );

            $image = $data->only('image')->toArray();

            if (! empty($image)) {
                $path = $image['image']->storePublicly('images');
                $post->update(['thumbnail' => config('app.url').Storage::url($path)]);
            }

            return responseOk('Пост успешно добавлен');
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

        $data = $putRequest->data($putRequest);

        return $this->handlePutUpdate($data);
    }

    private function handlePutUpdate(UpdatePostData $data): JsonResponse
    {

        $fillFields = (array_keys(get_object_vars($data)));

        $updateData = $data->toArray();

        try {
            foreach ($fillFields as $field) {
                if (! array_key_exists($field, $updateData)) {
                    $updateData[$field] = null;
                }
            }

            $this->post->update($updateData);

            return responseOk('Пост успешно обновлён');
        } catch (\Exception $e) {
            return responseFail($e, 'Ошибка обновления поста', 500);
        }
    }

    public function PatchUpdatePost(UpdatePostData $data): JsonResponse
    {
        try {
            if (! empty($data->toArray())) {
                $this->post->update($data->toArray());
            } else {
                return response()->json(['No Content' => 'Нет данных для обновления'], 204);
            }

            return responseOk('Пост успешно обновлён');
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
