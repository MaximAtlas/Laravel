<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use Illuminate\Validation\Rules\Enum;

class PutPostRequest extends ApiRequest
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
            'title' => ['required', 'string'],
            'content' => ['nullable'],
            'state' => ['required', new Enum(PostStatus::class)],
            'category_name' => ['required', 'exists:categories,name'],
        ];
    }
}
