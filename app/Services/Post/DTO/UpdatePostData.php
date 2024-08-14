<?php

namespace App\Services\Post\DTO;

use App\Enums\PostStatus;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdatePostData extends Data
{
    public string|Optional $title;

    #[MapInputName('content')]
    public string|Optional $body;

    #[MapInputName('state')]
    public PostStatus|Optional $status;

    public int|Optional $category_id;
}
