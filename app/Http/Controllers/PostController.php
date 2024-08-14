<?php

namespace App\Http\Controllers;

use App\Facades\Post as PostFacade;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\MinifiedPostResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return MinifiedPostResource::collection(PostFacade::getPublished());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {

        return PostFacade::addPost($request->data($request));

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
    public function update(Post $post, UpdatePostRequest $updateRequest): JsonResponse
    {
        PostFacade::setPost($post);

        $BoolPutRequest = $updateRequest->isMethod('put') ?? false;

        if ($BoolPutRequest) {

            return PostFacade::putValidationToUpdatePost($updateRequest);

        } else {
            return PostFacade::PatchUpdatePost($updateRequest);
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

    public function comment(Post $post, Request $request): PostCommentResource
    {
        // TODO: сделать валидацию данных коментария
        $comment = PostFacade::setPost($post)->comment($request);

        return new PostCommentResource($comment);
    }
}
