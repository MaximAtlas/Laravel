<?php

namespace App\Services\Post\DTO;

use App\Enums\PostStatus;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreatePostData extends Data
{
    public string $title;

    #[MapInputName('content')]
    public string|Optional $body;

    public UploadedFile $image;

    #[MapInputName('state')]
    public PostStatus $status;

    public int $category_id;
}
