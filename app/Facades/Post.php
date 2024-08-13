<?php

namespace App\Facades;

use App\Services\Post\PostService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static setPost
 * @method static getPublished
 * @method static addPost
 * @method static putValidationToUpdatePost
 * @method static handlePutUpdate
 * @method static PatchUpdatePost
 * @method static comment
 * @method static takeCategoryId
 *
 * @see PostService
 */
class Post extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'post';
    }
}
