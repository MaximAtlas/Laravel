<?php

namespace App\Http\Requests\Post;

use App\Enums\PostStatus;
use App\Facades\Post;
use App\Services\Post\DTO\UpdatePostData;
use Illuminate\Validation\Rules\Enum;

class PutPostRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'title' => ['required', 'string'],
            'content' => ['nullable'],
            'state' => ['required', new Enum(PostStatus::class)],
            'category_name' => ['required', 'exists:categories,name'],
        ];
    }

    public function data(PutPostRequest $request): UpdatePostData
    {

        $data = $request->toArray();

        if (! empty($data['category_name'])) {
            $categoryId = Post::takeCategoryId($request);
            $data['category_id'] = $categoryId->id;
            unset($data['category_name']);
        }

        return UpdatePostData::from($data);
    }
}
