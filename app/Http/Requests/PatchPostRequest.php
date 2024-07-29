<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use Illuminate\Validation\Rules\Enum;

class PatchPostRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /*    public function authorize(): bool
        {
            return false;
        }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'content' => ['nullable'],
            'state' => ['nullable', new Enum(PostStatus::class)],
            'category_name' => ['nullable', 'exists:categories,name'],
        ];
    }
}
