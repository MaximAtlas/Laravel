<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\MinifiedPostResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PostService $post): AnonymousResourceCollection
    {
        return MinifiedPostResource::collection($post->getPublished());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, PostService $postService): PostResource
    {

        $category = $postService->takeCategoryId($request);

        $post = $postService->addPost($request, $category);

        return new PostResource($post);

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, UpdatePostRequest $updateRequest, PostService $postService): JsonResponse
    {
        $postService->setPost($post);

        $BoolPutRequest = $updateRequest->isMethod('put') ?? false;

        if ($BoolPutRequest) {

            return $postService->putValidationToUpdatePost($updateRequest);

        } else {
            return $postService->PatchUpdatePost($updateRequest);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        try {

            $post->delete();

            return responseSuccess('Пост успешно удален');

        } catch (\Exception $e) {
            return responseFail($e, 'Ошибка удаления поста', 406);
        }
    }

    public function comment(Post $post, Request $request, PostService $postService): PostCommentResource
    {
        // TODO: сделать валидацию данных коментария
        $comment = $postService->setPost($post)->comment($request);

        return new PostCommentResource($comment);
    }
}
