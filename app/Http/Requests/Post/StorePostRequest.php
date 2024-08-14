<?php

namespace App\Http\Requests\Post;

use App\Enums\PostStatus;
use App\Facades\Post;
use App\Services\Post\DTO\CreatePostData;
use Illuminate\Validation\Rules\Enum;

class StorePostRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool         //Класс отвечающий за проверку прав
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:150'],
            'content' => ['nullable', 'string'],
            'image' => ['image', 'max:512'],
            'state' => ['required', new Enum(PostStatus::class)],
            'category_name' => ['required', 'exists:categories,name'],
        ];
    }

    public function data(StorePostRequest $request)
    {

        $categoryId = Post::takeCategoryId($request);

        $data = $this->validated();

        $data['category_id'] = $categoryId->id;
        unset($data['category_name']);

        return CreatePostData::from($data);
    }
}
