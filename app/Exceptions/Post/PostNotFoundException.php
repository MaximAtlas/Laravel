<?php

namespace App\Exceptions\Post;

use Exception;
use Throwable;

class PostNotFoundException extends Exception
{
    public function __construct(string $message = null, int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message ?? __('messages.PostNotFound'), $code, $previous);
    }
}
